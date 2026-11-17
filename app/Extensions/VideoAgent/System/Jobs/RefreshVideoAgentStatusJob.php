<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Jobs;

use App\Extensions\VideoAgent\System\Models\VideoAgentSession;
use App\Extensions\VideoAgent\System\Services\VideoAgentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Polling fallback for a Video Agent session. The webhook is the primary
 * signal; this job covers missed deliveries and local installs that HeyGen
 * cannot reach. It re-dispatches itself while the session is still moving.
 */
class RefreshVideoAgentStatusJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 120;

    public int $tries = 3;

    public int $uniqueFor = 60;

    public function __construct(public int $sessionId) {}

    public function uniqueId(): string
    {
        return (string) $this->sessionId;
    }

    public function handle(VideoAgentService $service): void
    {
        $session = VideoAgentSession::query()->find($this->sessionId);

        if ($session === null || $session->isTerminal()) {
            return;
        }

        $session = $service->sync($session);

        foreach ($session->videos()->unfinished()->visibleToUser()->get() as $video) {
            $service->syncVideo($video);
        }

        $this->scheduleNextPoll($session);
    }

    private function scheduleNextPoll(VideoAgentSession $session): void
    {
        $session->refresh();

        if ($session->isTerminal() || $session->status->needsUser()) {
            return;
        }

        if ($session->poll_attempts >= (int) config('video-agent.poll.max_attempts', 240)) {
            return;
        }

        // Running on the sync queue means "re-dispatch" would recurse straight
        // into another HTTP call, so leave it to the poll command / UI instead.
        if (config('queue.default') === 'sync') {
            return;
        }

        $interval = (int) setting('video_agent_poll_interval', config('video-agent.poll.interval_seconds', 15));

        self::dispatch($session->getKey())->delay(now()->addSeconds(max(5, $interval)));
    }
}
