<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Console\Commands;

use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Models\VideoAgentSession;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Services\DigitalTwinService;
use App\Extensions\VideoAgent\System\Services\VideoAgentService;
use Illuminate\Console\Command;

/**
 * Sweeps sessions and videos HeyGen never told us about — a missed webhook, an
 * install HeyGen cannot reach, or a queue worker that was down.
 */
class VideoAgentPollCommand extends Command
{
    protected $signature = 'video-agent:poll {--limit=50 : Maximum rows to refresh in one pass}';

    protected $description = 'Refresh stale HeyGen Video Agent sessions and renders';

    public function handle(VideoAgentService $service, DigitalTwinService $twins): int
    {
        $limit = (int) $this->option('limit');
        $staleAfter = now()->subSeconds((int) config('video-agent.poll.stale_after', 60));
        $maxAttempts = (int) config('video-agent.poll.max_attempts', 240);

        $sessions = VideoAgentSession::query()
            ->awaitingHeyGen()
            ->whereNotNull('heygen_session_id')
            ->where('poll_attempts', '<', $maxAttempts)
            ->where(fn ($query) => $query->whereNull('last_polled_at')->orWhere('last_polled_at', '<=', $staleAfter))
            ->limit($limit)
            ->get();

        foreach ($sessions as $session) {
            $service->sync($session);
        }

        $videos = VideoAgentVideo::query()
            ->unfinished()
            ->visibleToUser()
            ->whereNotNull('heygen_id')
            ->where('poll_attempts', '<', $maxAttempts)
            ->where(fn ($query) => $query->whereNull('last_polled_at')->orWhere('last_polled_at', '<=', $staleAfter))
            ->limit($limit)
            ->get();

        $service->syncVideosInBulk($videos);

        $avatars = VideoAgentAvatar::query()
            ->whereNotNull('user_id')
            ->where('avatar_type', 'digital_twin')
            ->where('status', '!=', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->where(function ($query): void {
                $query->where('status', '!=', 'completed')->orWhere('consent_status', '!=', 'accepted');
            })
            ->where(fn ($query) => $query->whereNull('last_polled_at')->orWhere('last_polled_at', '<=', $staleAfter))
            ->limit($limit)
            ->get();

        foreach ($avatars as $avatar) {
            $twins->refresh($avatar);
        }

        $this->info(sprintf('Refreshed %d session(s), %d video(s), and %d avatar(s).', $sessions->count(), $videos->count(), $avatars->count()));

        return self::SUCCESS;
    }
}
