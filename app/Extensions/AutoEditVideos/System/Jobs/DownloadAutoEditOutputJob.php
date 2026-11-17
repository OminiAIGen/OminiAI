<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Jobs;

use App\Extensions\AutoEditVideos\System\Models\AutoEditRender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/**
 * Rendley's storage URLs are time limited, so the finished file is copied to
 * our own disk as soon as the job completes.
 */
class DownloadAutoEditOutputJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public int $tries = 2;

    public function __construct(public int $renderId, public string $remoteUrl) {}

    public function handle(): void
    {
        $render = AutoEditRender::query()->find($this->renderId);

        if (! $render || filled($render->local_path)) {
            return;
        }

        $disk = (string) config('auto-edit-videos.storage.disk', 'uploads');

        $relativePath = sprintf(
            '%s/u-%d/auto-edit/render_%s.mp4',
            trim((string) config('auto-edit-videos.storage.directory', 'media/videos'), '/'),
            $render->user_id,
            $render->uuid,
        );

        try {
            $absolutePath = Storage::disk($disk)->path($relativePath);
            $directory = dirname($absolutePath);

            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                throw new RuntimeException('Could not create ' . $directory);
            }

            $response = Http::timeout(300)->sink($absolutePath)->get($this->remoteUrl);

            if (! $response->successful()) {
                throw new RuntimeException('Download returned status ' . $response->status());
            }

            $render->forceFill([
                'local_path' => $relativePath,
            ])->save();
        } catch (Throwable $e) {
            // The remote URL still works for a while, so a failed copy is not
            // worth failing the render over — the user can still watch it.
            Log::warning('AutoEditVideos: could not store the rendered file locally', [
                'render_id' => $render->getKey(),
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
