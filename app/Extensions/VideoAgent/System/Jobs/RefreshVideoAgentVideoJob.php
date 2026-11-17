<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Jobs;

use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Services\VideoAgentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Polling fallback for a directly generated render (no agent session behind
 * it). Re-dispatches while the render is still moving.
 */
class RefreshVideoAgentVideoJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 120;

    public int $tries = 3;

    public int $uniqueFor = 60;

    public function __construct(public int $videoId) {}

    public function uniqueId(): string
    {
        return (string) $this->videoId;
    }

    public function handle(VideoAgentService $service): void
    {
        $video = VideoAgentVideo::query()->find($this->videoId);

        if ($video === null || $video->status->isTerminal()) {
            return;
        }

        $video = $service->syncVideo($video);

        if ($video->status->isTerminal() || config('queue.default') === 'sync') {
            return;
        }

        if ($video->poll_attempts >= (int) config('video-agent.poll.max_attempts', 240)) {
            return;
        }

        $interval = (int) setting('video_agent_poll_interval', config('video-agent.poll.interval_seconds', 15));

        self::dispatch($video->getKey())->delay(now()->addSeconds(max(5, $interval)));
    }
}
