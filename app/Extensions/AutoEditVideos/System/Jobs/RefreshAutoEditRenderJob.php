<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Jobs;

use App\Extensions\AutoEditVideos\System\Enums\RenderStatusEnum;
use App\Extensions\AutoEditVideos\System\Enums\SessionStatusEnum;
use App\Extensions\AutoEditVideos\System\Models\AutoEditRender;
use App\Extensions\AutoEditVideos\System\Services\AutoEditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RefreshAutoEditRenderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 300;

    public int $tries = 3;

    public function __construct(public int $renderId) {}

    public function handle(AutoEditService $service): void
    {
        $render = AutoEditRender::query()->find($this->renderId);

        if (! $render || $render->isTerminal()) {
            return;
        }

        if ($render->poll_attempts >= (int) config('auto-edit-videos.poll.max_attempts', 240)) {
            $render->forceFill([
                'status'        => RenderStatusEnum::Failed,
                'error_message' => __('Rendley did not finish this render in time.'),
            ])->save();

            $render->session?->forceFill(['status' => SessionStatusEnum::Ready])->save();

            return;
        }

        $render = $service->syncRender($render);

        if (! $render->isTerminal() && $this->canReschedule()) {
            static::dispatch($render->getKey())->delay(now()->addSeconds($this->nextDelay($render->poll_attempts)));
        }
    }

    /**
     * The `sync` driver ignores delays and runs a dispatch inline, so
     * re-dispatching there would recurse until the attempt ceiling instead of
     * polling. On those installs `auto-edit-videos:poll` does the sweeping.
     */
    private function canReschedule(): bool
    {
        return config('queue.default') !== 'sync';
    }

    public function failed(Throwable $e): void
    {
        AutoEditRender::query()
            ->whereKey($this->renderId)
            ->whereIn('status', [RenderStatusEnum::Queued->value, RenderStatusEnum::Processing->value])
            ->update([
                'status'        => RenderStatusEnum::Failed->value,
                'error_message' => $e->getMessage(),
            ]);
    }

    private function nextDelay(int $attempts): int
    {
        return $attempts >= (int) config('auto-edit-videos.poll.backoff_after', 24)
            ? (int) config('auto-edit-videos.poll.backoff_seconds', 10)
            : (int) config('auto-edit-videos.poll.interval_seconds', 5);
    }
}
