<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Console\Commands;

use App\Extensions\PhoneCallAgent\System\Enums\CallStatusEnum;
use App\Extensions\PhoneCallAgent\System\Enums\DirectionEnum;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgent;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCall;
use App\Extensions\PhoneCallAgent\System\Services\PhoneCallAgentLimitService;
use App\Extensions\PhoneCallAgent\System\Services\PhoneCallAgentService;
use App\Extensions\PhoneCallAgent\System\Services\Providers\TwilioPhoneService;
use App\Helpers\Classes\Helper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Answers "why is this outbound call still Scheduled?" in one shot, without waiting on cron.
 *
 * Every stall in the dispatch path (dead cron, stale overlap lock, exhausted daily cap, missing
 * provider credentials, no queue worker) is checked here and reported explicitly.
 */
class DiagnoseOutboundCalls extends Command
{
    protected $signature = 'phone-call-agent:diagnose-outbound
        {--call= : Dial this call id synchronously (no queue, no cron) and print the raw provider error}
        {--explain= : Report why one call (uuid or id) has not fired yet, without dialling it}';

    protected $description = 'Diagnose why due outbound phone calls are not being dispatched.';

    private const SCHEDULED_COMMAND = 'phone-call-agent:dispatch-due-outbound';

    private const STATUS_ROUTE = 'api.phone-call-agent.webhook.twilio.status';

    public function handle(): int
    {
        if ($callId = $this->option('call')) {
            return $this->dialNow((int) $callId);
        }

        if ($reference = $this->option('explain')) {
            return $this->explainCall((string) $reference);
        }

        $this->checkScheduler();
        $this->checkDemoMode();
        $this->checkTime();
        $this->checkQueue();
        $this->checkWebhooks();
        $this->reportStuckCalls();

        $due = $this->dueCalls();

        $this->reportDueCalls($due);
        $this->reportLimits($due);
        $this->reportAgents($due);

        return self::SUCCESS;
    }

    /**
     * The status callback URL is built here and handed to Twilio, so it has to both resolve in this
     * process and be reachable from the public internet. A failure on either side leaves rows sitting
     * at "In Progress" with no result ever reported back.
     */
    private function checkWebhooks(): void
    {
        $this->newLine();
        $this->line('<options=bold>Webhooks</>');

        $host = TwilioPhoneService::publicHost();

        $this->ok("Public host: {$host}");

        if (! setting('phone_call_agent_public_host')) {
            $this->warn_('phone_call_agent_public_host is not set; falling back to the APP_URL host. Twilio must be able to reach it.');
        }

        if (preg_match('/^(localhost|127\.\d+\.\d+\.\d+|0\.0\.0\.0|.*\.test|.*\.local)$/i', $host)) {
            $this->fail_("The public host \"{$host}\" is not reachable from the internet, so provider status callbacks will never arrive.");
        }

        try {
            $url = route(self::STATUS_ROUTE);

            $this->ok("Status callback URL: {$url}");
        } catch (Throwable $e) {
            $this->fail_(
                'Route [' . self::STATUS_ROUTE . '] is not registered in this process. '
                . 'Clear a compiled route cache (php artisan route:clear) and restart the queue worker (php artisan queue:restart).'
            );
            $this->warn_("Dialling will fall back to: https://{$host}/api/phone-call-agent/webhook/twilio/status");
        }
    }

    /**
     * Rows that took the Queued claim and never progressed. These are the "In Progress" rows in the
     * UI; past the configured windows the dispatch command's reaper will fail them.
     */
    private function reportStuckCalls(): void
    {
        $this->newLine();
        $this->line('<options=bold>Stuck rows (claimed but not finished)</>');

        $stuck = ExtPhoneCallAgentCall::query()
            ->where('direction', DirectionEnum::Outbound->value)
            ->whereIn('status', [
                CallStatusEnum::Queued->value,
                CallStatusEnum::Ringing->value,
                CallStatusEnum::Answered->value,
            ])
            ->whereNull('ended_at')
            ->orderBy('id')
            ->get();

        if ($stuck->isEmpty()) {
            $this->ok('No rows are stuck mid-flight.');

            return;
        }

        $undialedCutoff = now()->subMinutes((int) config('phone-call-agent.outbound.stuck_undialed_minutes', 15));
        $dialedCutoff = now()->subMinutes((int) config('phone-call-agent.outbound.stuck_dialed_minutes', 60));

        $this->warn_($stuck->count() . ' row(s) claimed but never finished.');

        $this->table(
            ['id', 'status', 'started_at (UTC)', 'age', 'bucket'],
            $stuck->map(function (ExtPhoneCallAgentCall $call) use ($undialedCutoff, $dialedCutoff): array {
                $bucket = $call->started_at === null
                    ? ($call->updated_at?->lte($undialedCutoff) ? 'never dialled — will be reaped' : 'never dialled')
                    : ($call->started_at->lte($dialedCutoff) ? 'no status callback — will be reaped' : 'awaiting status callback');

                return [
                    $call->id,
                    $call->status?->value,
                    $call->started_at?->toDateTimeString() ?? '—',
                    $call->updated_at?->diffForHumans(),
                    $bucket,
                ];
            })->all(),
        );

        if ($stuck->whereNull('started_at')->isNotEmpty()) {
            $this->fail_('Rows with no started_at were claimed by a worker that died before dialling. Check the worker (php artisan queue:restart) and storage/logs.');
        }
    }

    /**
     * The app's own heartbeat: CrontabCheckCommand writes this key every minute from schedule:run.
     * A missing or stale value means OS cron is not running, which stalls every scheduled call.
     */
    private function checkScheduler(): void
    {
        $this->newLine();
        $this->line('<options=bold>Scheduler</>');

        $lastRun = Cache::get('crontab_check');
        $cronAlive = false;

        if (! $lastRun) {
            $this->fail_('schedule:run has never been recorded (cache key "crontab_check" is empty). Check the OS crontab.');
        } else {
            $age = (int) now()->diffInSeconds($lastRun, true);
            $cronAlive = $age <= 120;

            $cronAlive
                ? $this->ok("schedule:run last ran {$age}s ago.")
                : $this->fail_("schedule:run last ran {$age}s ago — OS cron looks dead.");
        }

        $this->reportDispatchHeartbeat($cronAlive);

        $event = $this->scheduledEvent();

        if (! $event) {
            $this->fail_(self::SCHEDULED_COMMAND . ' is NOT registered in the scheduler. The extension provider is not booting in console.');

            return;
        }

        $this->ok(self::SCHEDULED_COMMAND . " is registered ({$event->expression}).");

        try {
            $event->mutex?->exists($event)
                ? $this->warn_('The withoutOverlapping lock is currently held. If no run is in flight this is a stale lock blocking every tick — clear the cache.')
                : $this->ok('No stale overlap lock.');
        } catch (Throwable $e) {
            $this->warn_('Could not read the overlap lock: ' . $e->getMessage());
        }
    }

    private function scheduledEvent(): ?Event
    {
        foreach (app(Schedule::class)->events() as $event) {
            if (str_contains((string) $event->command, self::SCHEDULED_COMMAND)) {
                return $event;
            }
        }

        return null;
    }

    /**
     * The cron heartbeat only proves schedule:run fired. This one proves the dispatch command itself
     * ran — the difference between the two is the silent stall: a held overlap lock, or the extension
     * provider not booting in the cron process. Neither writes a log line of its own.
     */
    private function reportDispatchHeartbeat(bool $cronAlive): void
    {
        $lastDispatch = Cache::get(DispatchDueOutboundCalls::HEARTBEAT_KEY);

        if (! $lastDispatch) {
            $this->fail_(
                self::SCHEDULED_COMMAND . ' has never recorded a run. '
                . 'If cron is alive, the run is being blocked — clear a stale overlap lock (php artisan cache:clear) '
                . 'and confirm the extension provider boots in the cron process.'
            );

            return;
        }

        $age = (int) now()->diffInSeconds($lastDispatch, true);

        if ($age <= 120) {
            $this->ok(self::SCHEDULED_COMMAND . " last ran {$age}s ago.");

            return;
        }

        $cronAlive
            ? $this->fail_(
                self::SCHEDULED_COMMAND . " last ran {$age}s ago while cron is alive — the run is being blocked. "
                . 'Most likely a stale withoutOverlapping lock (php artisan cache:clear).'
            )
            : $this->warn_(self::SCHEDULED_COMMAND . " last ran {$age}s ago, consistent with cron being down.");
    }

    /**
     * PHP runs in config('app.timezone'), but MySQL converts TIMESTAMP columns using its own session
     * zone. The round trip only stays correct while every process shares that zone, so both clocks are
     * printed here — and every time this command prints elsewhere is UTC, to be read against now().
     */
    private function checkTime(): void
    {
        $this->newLine();
        $this->line('<options=bold>Time</>');

        $this->ok('PHP timezone: ' . date_default_timezone_get() . ' (config/app.php: ' . config('app.timezone') . ').');
        $this->ok('now(): ' . now()->toIso8601String());

        $connection = config('database.default');

        if (config("database.connections.{$connection}.driver") !== 'mysql') {
            $this->ok("Database driver is not mysql ({$connection}); no session timezone to compare.");

            return;
        }

        try {
            $clock = DB::selectOne('SELECT @@session.time_zone AS session_tz, NOW() AS db_now, UTC_TIMESTAMP() AS db_utc, TIMESTAMPDIFF(SECOND, UTC_TIMESTAMP(), NOW()) AS skew');
        } catch (Throwable $e) {
            $this->warn_('Could not read the database clock: ' . $e->getMessage());

            return;
        }

        $this->ok("MySQL session timezone: {$clock->session_tz} (NOW() {$clock->db_now}, UTC_TIMESTAMP() {$clock->db_utc}).");

        $skew = (int) $clock->skew;

        if ($skew !== 0) {
            $this->warn_(
                "The database clock is {$skew}s from UTC while PHP is " . config('app.timezone') . '. '
                . 'TIMESTAMP columns are stored in the database session zone, so scheduled_at only reads back correctly '
                . 'while every process connects with this same zone.'
            );
        }

        $this->reportDatabaseDst();
    }

    /**
     * A database session zone that observes DST shifts every stored TIMESTAMP by an hour twice a year,
     * which shows up as scheduled calls firing an hour early or late for part of the year.
     * UNIX_TIMESTAMP interprets its literal in the session zone, so comparing two literals six months
     * apart against their UTC epochs reveals the offset on each side of a transition. CONVERT_TZ is
     * avoided deliberately: it returns NULL unless the mysql.time_zone* tables are populated.
     */
    private function reportDatabaseDst(): void
    {
        $winter = '2026-01-15 12:00:00';
        $summer = '2026-07-15 12:00:00';

        try {
            $epochs = DB::selectOne(
                'SELECT UNIX_TIMESTAMP(?) AS winter, UNIX_TIMESTAMP(?) AS summer',
                [$winter, $summer],
            );
        } catch (Throwable $e) {
            $this->warn_('Could not probe the database timezone for DST: ' . $e->getMessage());

            return;
        }

        $winterOffset = Carbon::parse($winter, 'UTC')->getTimestamp() - (int) $epochs->winter;
        $summerOffset = Carbon::parse($summer, 'UTC')->getTimestamp() - (int) $epochs->summer;

        if ($winterOffset === $summerOffset) {
            $this->ok('The database timezone has a fixed offset; stored timestamps do not shift seasonally.');

            return;
        }

        $this->fail_(
            'The database timezone observes DST (offset ' . $winterOffset . 's in January vs ' . $summerOffset . 's in July). '
            . 'Every stored TIMESTAMP shifts by the difference at each transition, so scheduled calls misfire by that amount '
            . 'for part of the year.'
        );
    }

    private function checkDemoMode(): void
    {
        $this->newLine();
        $this->line('<options=bold>Environment</>');

        Helper::appIsDemo()
            ? $this->fail_('Demo mode is ON — the dispatch command and the job both return immediately.')
            : $this->ok('Demo mode is off.');
    }

    /**
     * A row that flipped to "queued" but never dialled usually means the job is sitting in the queue
     * with no worker consuming it.
     */
    private function checkQueue(): void
    {
        $this->newLine();
        $this->line('<options=bold>Queue</>');

        $connection = config('queue.default');
        $driver = config("queue.connections.{$connection}.driver");

        $this->ok("Connection: {$connection} (driver: {$driver}).");

        if ($driver !== 'database' || ! Schema::hasTable('jobs')) {
            return;
        }

        // The framework's jobs/failed_jobs tables have no Eloquent model, so query them directly.
        $pending = DB::table('jobs')->where('payload', 'like', '%PlaceOutboundCallJob%')->count();
        $oldest = DB::table('jobs')->where('payload', 'like', '%PlaceOutboundCallJob%')->min('created_at');

        if ($pending > 0) {
            $age = $oldest ? (int) now()->diffInSeconds(Carbon::createFromTimestamp((int) $oldest), true) : 0;

            $this->warn_("{$pending} PlaceOutboundCallJob job(s) waiting, oldest {$age}s old. If this number never drops, no queue:work is running.");
        } else {
            $this->ok('No PlaceOutboundCallJob backlog.');
        }

        if (Schema::hasTable('failed_jobs')) {
            $failed = DB::table('failed_jobs')->where('payload', 'like', '%PlaceOutboundCallJob%')->count();

            $failed > 0
                ? $this->warn_("{$failed} failed PlaceOutboundCallJob entr(ies). Inspect with: php artisan queue:failed")
                : $this->ok('No failed PlaceOutboundCallJob entries.');
        }
    }

    /**
     * The exact set DispatchDueOutboundCalls scans.
     *
     * @return Collection<int, ExtPhoneCallAgentCall>
     */
    private function dueCalls(): Collection
    {
        return ExtPhoneCallAgentCall::query()
            ->with(['agent', 'user'])
            ->where('direction', DirectionEnum::Outbound->value)
            ->where('status', CallStatusEnum::Pending->value)
            ->where(function ($query): void {
                $query->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            })
            ->orderBy('id')
            ->get();
    }

    private function reportDueCalls(Collection $due): void
    {
        $this->newLine();
        $this->line('<options=bold>Due rows (outbound + pending, scheduled at or before now)</>');

        if ($due->isEmpty()) {
            $this->ok('Nothing is due. Rows shown as "Scheduled" in the UI are still in the future.');

            return;
        }

        $this->warn_($due->count() . ' row(s) are due but still pending.');

        $this->table(
            ['id', 'user', 'agent', 'to', 'scheduled_at (UTC)', 'created_at (UTC)', 'waiting'],
            $due->map(fn (ExtPhoneCallAgentCall $call): array => [
                $call->id,
                $call->user_id,
                $call->agent?->title ?? 'MISSING',
                $call->called_number,
                $call->scheduled_at?->toDateTimeString() ?? 'immediate',
                $call->created_at?->toDateTimeString(),
                $call->created_at?->diffForHumans(),
            ])->all(),
        );
    }

    /**
     * A remaining count of 0 makes the dispatch command take(0) — no dispatch, no log, forever.
     */
    private function reportLimits(Collection $due): void
    {
        if ($due->isEmpty()) {
            return;
        }

        $this->newLine();
        $this->line('<options=bold>Plan limits for the users owning those rows</>');

        foreach ($due->pluck('user')->filter()->unique('id') as $user) {
            $limits = app(PhoneCallAgentLimitService::class, ['user' => $user]);
            $remaining = $limits->remainingOutboundCallsToday();
            $decision = $limits->canPlaceOutboundCall();

            $label = "user #{$user->id}: outbound_enabled=" . ($limits->isOutboundEnabled() ? 'yes' : 'no')
                . ', calls_left_today=' . ($remaining === null ? 'unlimited' : $remaining)
                . ', seconds_left=' . ($limits->remainingOutboundSeconds() ?? 'unlimited');

            if ($remaining === 0 || ! $decision['allowed']) {
                $this->fail_($label . ' — BLOCKED: ' . ($decision['reason'] ?? 'daily call cap reached'));

                $looping = $due->where('user_id', $user->id)
                    ->filter(fn (ExtPhoneCallAgentCall $call): bool => $call->campaign_id !== null || $call->scheduled_at !== null);

                if ($looping->isNotEmpty()) {
                    $this->warn_(
                        'Scheduled/campaign rows for this user are deferred back to pending and re-dispatched every minute rather than progressing: '
                        . $looping->pluck('id')->implode(', ')
                    );
                }
            } else {
                $this->ok($label);
            }
        }

        $orphans = $due->whereNull('user')->pluck('id');

        if ($orphans->isNotEmpty()) {
            $this->fail_('Rows with a missing user (can never dial): ' . $orphans->implode(', '));
        }
    }

    /**
     * Exactly what PhoneCallAgentService::initiateOutboundCall() throws on. Presence only — never
     * print credential values.
     */
    private function reportAgents(Collection $due): void
    {
        if ($due->isEmpty()) {
            return;
        }

        $this->newLine();
        $this->line('<options=bold>Agent readiness</>');

        foreach ($due->pluck('agent')->filter()->unique('id') as $agent) {
            $missing = $this->missingAgentFields($agent);

            $label = "agent #{$agent->id} \"{$agent->title}\" (" . $agent->provider->value . ')';

            $missing === []
                ? $this->ok($label . ' is dialable.')
                : $this->fail_($label . ' is missing: ' . implode(', ', $missing));
        }
    }

    /**
     * @return list<string>
     */
    private function missingAgentFields(ExtPhoneCallAgent $agent): array
    {
        $missing = [];

        if ($agent->provider->value === 'twilio') {
            foreach (['twilio_account_sid', 'twilio_auth_token', 'phone_number'] as $field) {
                if (! $agent->{$field}) {
                    $missing[] = $field;
                }
            }

            return $missing;
        }

        foreach (['agent_id', 'phone_number_id'] as $field) {
            if (! $agent->{$field}) {
                $missing[] = $field;
            }
        }

        return $missing;
    }

    /**
     * The whole verdict for a single row, so "the call wasn't made" can be answered without
     * correlating four sections of the full report by hand. Read-only: it never dials.
     */
    private function explainCall(string $reference): int
    {
        $call = ExtPhoneCallAgentCall::query()
            ->with(['agent', 'user'])
            ->when(ctype_digit($reference), fn ($query) => $query->whereKey((int) $reference))
            ->when(! ctype_digit($reference), fn ($query) => $query->where('uuid', $reference))
            ->first();

        if (! $call) {
            $this->fail_("No call matches \"{$reference}\" (pass a uuid or a numeric id).");

            return self::FAILURE;
        }

        $this->line('<options=bold>Call</>');
        $this->ok("#{$call->id} {$call->uuid} — {$call->direction?->value}/{$call->status?->value}, source {$call->source?->value}"
            . ($call->campaign_id ? ", campaign #{$call->campaign_id}" : '')
            . ", to {$call->called_number}");

        $call->status_reason
            ? $this->warn_('status_reason: ' . $call->status_reason)
            : $this->ok('status_reason is empty.');

        $this->explainTiming($call);
        $this->explainDue($call);
        $this->explainLimits($call);
        $this->explainQueue($call);
        $this->explainAgent($call);

        return self::SUCCESS;
    }

    /**
     * Every instant here is UTC, printed next to now() so a genuine clock complaint is falsifiable
     * from this output alone.
     */
    private function explainTiming(ExtPhoneCallAgentCall $call): void
    {
        $this->newLine();
        $this->line('<options=bold>Timing (UTC)</>');

        $this->ok('now():        ' . now()->toDateTimeString());
        $this->ok('created_at:   ' . ($call->created_at?->toDateTimeString() ?? '—'));
        $this->ok('scheduled_at: ' . ($call->scheduled_at?->toDateTimeString() ?? 'immediate (no schedule)'));
        $this->ok('started_at:   ' . ($call->started_at?->toDateTimeString() ?? 'never dialled'));
        $this->ok('ended_at:     ' . ($call->ended_at?->toDateTimeString() ?? '—'));
        $this->ok('updated_at:   ' . ($call->updated_at?->toDateTimeString() ?? '—'));

        if (! $call->scheduled_at) {
            return;
        }

        $seconds = (int) now()->diffInSeconds($call->scheduled_at, false);

        if ($seconds > 0) {
            $this->ok("Scheduled {$seconds}s in the future.");

            return;
        }

        // Only an overdue row that is still pending is a symptom; on a finished row the same gap is
        // just how long ago the call happened.
        $call->status?->value === CallStatusEnum::Pending->value
            ? $this->warn_('Overdue by ' . abs($seconds) . 's and still pending.')
            : $this->ok('Was due ' . $call->scheduled_at->diffForHumans() . '.');
    }

    /**
     * Mirrors DispatchDueOutboundCalls' own query predicate by predicate, so a row that the command
     * cannot see is named as such rather than left to inference.
     */
    private function explainDue(ExtPhoneCallAgentCall $call): void
    {
        $this->newLine();
        $this->line('<options=bold>Dispatcher visibility</>');

        if ($call->direction?->value !== DirectionEnum::Outbound->value) {
            $this->fail_('Not an outbound row; the dispatch command never scans it.');

            return;
        }

        if ($call->status?->value !== CallStatusEnum::Pending->value) {
            $this->ok('Already past pending (' . $call->status?->value . '); the dispatch command is done with this row.');

            return;
        }

        if ($call->scheduled_at && $call->scheduled_at->isFuture()) {
            $this->ok('Pending and not due yet — it will be picked up on the first tick at or after scheduled_at.');

            return;
        }

        $this->warn_('Due now and still pending — the dispatch command should be picking it up every minute.');
    }

    private function explainLimits(ExtPhoneCallAgentCall $call): void
    {
        $this->newLine();
        $this->line('<options=bold>Plan limits</>');

        if (! $call->user) {
            $this->fail_('The owning user is missing; this row can never dial.');

            return;
        }

        $limits = app(PhoneCallAgentLimitService::class, ['user' => $call->user]);
        $decision = $limits->canPlaceOutboundCall();
        $remaining = $limits->remainingOutboundCallsToday();

        $label = "user #{$call->user->id}: outbound_enabled=" . ($limits->isOutboundEnabled() ? 'yes' : 'no')
            . ', calls_left_today=' . ($remaining === null ? 'unlimited' : $remaining)
            . ', seconds_left=' . ($limits->remainingOutboundSeconds() ?? 'unlimited');

        if ($decision['allowed']) {
            $this->ok($label);

            return;
        }

        $this->fail_($label . ' — BLOCKED: ' . $decision['reason']);

        if ($call->campaign_id || $call->scheduled_at) {
            $this->warn_('This row defers back to pending on every tick rather than failing, so it can wait indefinitely.');
        }
    }

    /**
     * A job for this exact row sitting in `jobs` means dispatch worked and nothing is consuming the
     * queue — the opposite conclusion from an empty queue with a pending row.
     */
    private function explainQueue(ExtPhoneCallAgentCall $call): void
    {
        $this->newLine();
        $this->line('<options=bold>Queue</>');

        if (config('queue.connections.' . config('queue.default') . '.driver') !== 'database' || ! Schema::hasTable('jobs')) {
            $this->ok('Queue driver is not database; per-row payloads cannot be inspected here.');

            return;
        }

        $waiting = $this->countJobsFor('jobs', $call->id);

        $waiting > 0
            ? $this->warn_("{$waiting} PlaceOutboundCallJob payload(s) for this row are waiting in the queue. Dispatch worked; no worker is consuming it.")
            : $this->ok('No queued job for this row.');

        if (! Schema::hasTable('failed_jobs')) {
            return;
        }

        $failed = $this->countJobsFor('failed_jobs', $call->id);

        $failed > 0
            ? $this->fail_("{$failed} failed PlaceOutboundCallJob entr(ies) for this row. Inspect with: php artisan queue:failed")
            : $this->ok('No failed job for this row.');
    }

    /**
     * The queued model arrives as a serialized ModelIdentifier inside the JSON payload, so the id is
     * matched against the decoded command string rather than with a LIKE over escaped JSON.
     */
    private function countJobsFor(string $table, int $callId): int
    {
        return DB::table($table)
            ->where('payload', 'like', '%PlaceOutboundCallJob%')
            ->pluck('payload')
            ->filter(function (string $payload) use ($callId): bool {
                $command = json_decode($payload, true)['data']['command'] ?? '';

                return str_contains((string) $command, '"id";i:' . $callId . ';');
            })
            ->count();
    }

    private function explainAgent(ExtPhoneCallAgentCall $call): void
    {
        $this->newLine();
        $this->line('<options=bold>Agent readiness</>');

        if (! $call->agent) {
            $this->fail_('The agent is missing; this row can never dial.');

            return;
        }

        $missing = $this->missingAgentFields($call->agent);
        $label = "agent #{$call->agent->id} \"{$call->agent->title}\" (" . $call->agent->provider->value . ')';

        $missing === []
            ? $this->ok($label . ' is dialable.')
            : $this->fail_($label . ' is missing: ' . implode(', ', $missing));
    }

    /**
     * Dial one row inline, bypassing cron and the queue, so the provider's own error is visible.
     */
    private function dialNow(int $callId): int
    {
        $call = ExtPhoneCallAgentCall::query()->with('agent')->find($callId);

        if (! $call) {
            $this->error("Call #{$callId} not found.");

            return self::FAILURE;
        }

        if (! $call->agent) {
            $this->error("Call #{$callId} has no agent.");

            return self::FAILURE;
        }

        $this->info("Dialling call #{$call->id} to {$call->called_number} via {$call->agent->provider->value}...");

        try {
            app(PhoneCallAgentService::class)->initiateOutboundCall($call);
        } catch (Throwable $e) {
            $this->error(get_class($e) . ': ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info("Dialled. call_sid={$call->fresh()->call_sid}");

        return self::SUCCESS;
    }

    private function ok(string $message): void
    {
        $this->line("  <fg=green>OK</>   {$message}");
    }

    private function warn_(string $message): void
    {
        $this->line("  <fg=yellow>WARN</> {$message}");
    }

    private function fail_(string $message): void
    {
        $this->line("  <fg=red>FAIL</> {$message}");
    }
}
