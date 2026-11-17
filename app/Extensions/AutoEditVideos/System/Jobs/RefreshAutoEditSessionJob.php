<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Jobs;

use App\Extensions\AutoEditVideos\System\Enums\SessionStatusEnum;
use App\Extensions\AutoEditVideos\System\Models\AutoEditSession;
use App\Extensions\AutoEditVideos\System\Services\AutoEditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Rendley has no webhooks, so an agent job is followed by re-dispatching this
 * until the session reaches a terminal state or the attempt ceiling is hit.
 */
class RefreshAutoEditSessionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 300;

    public int $tries = 3;

    public function __construct(public int $sessionId) {}

    public function handle(AutoEditService $service): void
    {
        $session = AutoEditSession::query()->find($this->sessionId);

        if (! $session || ! $session->status->isAwaitingAgent()) {
            return;
        }

        if ($session->poll_attempts >= (int) config('auto-edit-videos.poll.max_attempts', 240)) {
            $session->forceFill([
                'status'        => SessionStatusEnum::Failed,
                'error_message' => __('Rendley did not finish planning this edit in time.'),
            ])->save();

            return;
        }

        $session = $service->syncSession($session);

        if ($session->status->isAwaitingAgent() && $this->canReschedule()) {
            static::dispatch($session->getKey())->delay(now()->addSeconds($this->nextDelay($session->poll_attempts)));
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
        AutoEditSession::query()
            ->whereKey($this->sessionId)
            ->where('status', SessionStatusEnum::Planning->value)
            ->update([
                'status'        => SessionStatusEnum::Failed->value,
                'error_message' => $e->getMessage(),
            ]);
    }

    /**
     * Poll tightly at first, then back off — a long edit does not need the
     * same cadence as one that is about to finish.
     */
    private function nextDelay(int $attempts): int
    {
        return $attempts >= (int) config('auto-edit-videos.poll.backoff_after', 24)
            ? (int) config('auto-edit-videos.poll.backoff_seconds', 10)
            : (int) config('auto-edit-videos.poll.interval_seconds', 5);
    }
}
