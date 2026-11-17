<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Jobs;

use App\Extensions\VideoAgent\System\Enums\VideoStatusEnum;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Services\VideoAgentBillingService;
use App\Models\SettingTwo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * HeyGen output URLs are presigned and expire quickly, so the finished render
 * is copied to the configured disk as soon as it is reported complete. The
 * credit debit happens here because this is the first point where the real
 * duration is known.
 */
class DownloadVideoAgentOutputJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public int $tries = 3;

    public int $backoff = 30;

    public int $uniqueFor = 600;

    public function __construct(public int $videoId) {}

    public function uniqueId(): string
    {
        return (string) $this->videoId;
    }

    public function handle(VideoAgentBillingService $billing): void
    {
        $video = VideoAgentVideo::query()->with('user')->find($this->videoId);

        if ($video === null || $video->status !== VideoStatusEnum::Completed) {
            return;
        }

        if (blank($video->local_path) && filled($video->remote_url)) {
            $this->store($video);
        }

        if (blank($video->completed_at)) {
            $video->forceFill(['completed_at' => now()])->save();
        }

        $billing->debit($video);
    }

    private function store(VideoAgentVideo $video): void
    {
        try {
            $response = Http::timeout(300)->get((string) $video->remote_url);
        } catch (ConnectionException $e) {
            Log::warning('Video Agent output download failed', [
                'video_id' => $video->getKey(),
                'message'  => $e->getMessage(),
            ]);

            return;
        }

        if (! $response->successful()) {
            Log::warning('Video Agent output download returned an error', [
                'video_id' => $video->getKey(),
                'status'   => $response->status(),
            ]);

            return;
        }

        $relativePath = sprintf('video-agent/%s.%s', $video->uuid, $video->kind->isAudio() ? 'mp3' : 'mp4');

        // Local files live under public/uploads, which is the `uploads` disk
        // in this app — not the stock Laravel `public` disk.
        Storage::disk('uploads')->put($relativePath, $response->body());

        $target = SettingTwo::getCache()?->ai_image_storage;

        if (in_array($target, [VideoAgentVideo::STORAGE_AWS, VideoAgentVideo::STORAGE_R2], true)) {
            $remotePath = Storage::disk($target)->putFileAs(
                'video-agent',
                Storage::disk('uploads')->path($relativePath),
                basename($relativePath),
            );

            Storage::disk('uploads')->delete($relativePath);

            $video->forceFill([
                'local_path' => Storage::disk($target)->url($remotePath),
                'storage'    => $target,
            ])->save();

            return;
        }

        $video->forceFill([
            'local_path' => $relativePath,
            'storage'    => VideoAgentVideo::STORAGE_LOCAL,
        ])->save();

        $this->storeThumbnail($video);
    }

    /**
     * HeyGen thumbnail URLs are presigned and expire within days, so the still
     * is cached next to the video. A failed thumbnail never fails the video.
     */
    private function storeThumbnail(VideoAgentVideo $video): void
    {
        $remote = (string) $video->thumbnail_url;

        if (blank($remote) || ! str_starts_with($remote, 'http')) {
            return;
        }

        try {
            $response = Http::timeout(60)->get($remote);
        } catch (ConnectionException) {
            return;
        }

        if (! $response->successful() || blank($response->body())) {
            return;
        }

        $extension = match (true) {
            str_contains((string) $response->header('Content-Type'), 'png')  => 'png',
            str_contains((string) $response->header('Content-Type'), 'webp') => 'webp',
            default                                                          => 'jpg',
        };

        $relativePath = sprintf('video-agent/thumbs/%s.%s', $video->uuid, $extension);

        Storage::disk('uploads')->put($relativePath, $response->body());

        $video->forceFill([
            'thumbnail_url' => Storage::disk('uploads')->url($relativePath),
        ])->save();
    }
}
