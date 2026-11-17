<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Console\Commands;

use App\Extensions\AutoEditVideos\System\Models\AutoEditRender;
use App\Extensions\AutoEditVideos\System\Models\AutoEditSession;
use App\Extensions\AutoEditVideos\System\Services\AutoEditService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

/**
 * Sweeps edits and renders nothing else advanced — a queue worker that was
 * down, or an install still running the `sync` queue driver where the
 * re-dispatched poll job never got its own chance to run.
 */
class AutoEditVideosPollCommand extends Command
{
    protected $signature = 'auto-edit-videos:poll {--limit=50 : Maximum rows to refresh in one pass}';

    protected $description = 'Refresh stale Rendley auto edit sessions and renders';

    public function handle(AutoEditService $service): int
    {
        $limit = (int) $this->option('limit');
        $staleAfter = now()->subSeconds((int) config('auto-edit-videos.poll.stale_after', 60));
        $maxAttempts = (int) config('auto-edit-videos.poll.max_attempts', 240);

        $sessions = AutoEditSession::query()
            ->awaitingAgent()
            ->whereNotNull('rendley_job_id')
            ->where('poll_attempts', '<', $maxAttempts)
            ->where(fn (Builder $query) => $query->whereNull('last_polled_at')->orWhere('last_polled_at', '<=', $staleAfter))
            ->limit($limit)
            ->get();

        foreach ($sessions as $session) {
            $service->syncSession($session);
        }

        $renders = AutoEditRender::query()
            ->unfinished()
            ->whereNotNull('rendley_job_id')
            ->where('poll_attempts', '<', $maxAttempts)
            ->where(fn (Builder $query) => $query->whereNull('last_polled_at')->orWhere('last_polled_at', '<=', $staleAfter))
            ->limit($limit)
            ->get();

        $service->syncRendersInBulk($renders);

        $this->info(sprintf('Refreshed %d edit(s) and %d render(s).', $sessions->count(), $renders->count()));

        return self::SUCCESS;
    }
}
