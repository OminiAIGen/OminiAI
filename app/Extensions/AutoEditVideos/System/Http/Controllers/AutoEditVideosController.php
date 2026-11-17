<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Http\Controllers;

use App\Extensions\AutoEditVideos\System\Http\Requests\StoreAutoEditSessionRequest;
use App\Extensions\AutoEditVideos\System\Models\AutoEditRender;
use App\Extensions\AutoEditVideos\System\Models\AutoEditSession;
use App\Extensions\AutoEditVideos\System\Services\AutoEditBillingService;
use App\Extensions\AutoEditVideos\System\Services\AutoEditService;
use App\Extensions\AutoEditVideos\System\Services\Rendley\RendleyClient;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AutoEditVideosController extends Controller
{
    public function __construct(
        private readonly AutoEditService $autoEdit,
        private readonly AutoEditBillingService $billing,
        private readonly RendleyClient $client,
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        $renders = AutoEditRender::query()
            ->where('user_id', $user->id)
            ->with('session')
            ->latest('id')
            ->limit(24)
            ->get()
            ->map(fn (AutoEditRender $render): array => $this->studioEntry($render));

        $latestSession = AutoEditSession::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        return view('auto-edit-videos::index', [
            'userName'         => explode(' ', (string) ($user->name ?? ''))[0],
            'sessionsCount'    => AutoEditSession::query()->where('user_id', $user->id)->count(),
            'latestSessionUrl' => $latestSession ? route('dashboard.user.auto-edit-videos.sessions.show', $latestSession) : null,
            'todayEntries'     => $renders->filter(fn (array $entry): bool => (bool) ($entry['_ts'] && Carbon::createFromTimestamp($entry['_ts'])->isToday()))->values()->all(),
            'previousEntries'  => $renders->reject(fn (array $entry): bool => (bool) ($entry['_ts'] && Carbon::createFromTimestamp($entry['_ts'])->isToday()))->values()->all(),
            'sessions'         => $this->sessionsFor($user->id)->limit(30)->get(),
            'usage'            => $this->billing->usage($user),
            'isConfigured'     => $this->client->isConfigured(),
            'isDemo'           => Helper::appIsDemo(),
            'resolutions'      => (array) config('auto-edit-videos.resolutions', []),
            'aspectRatios'     => (array) config('auto-edit-videos.aspect_ratios', []),
            'durations'        => (array) config('auto-edit-videos.durations', []),
        ]);
    }

    public function library(Request $request): View
    {
        $renders = AutoEditRender::query()
            ->where('user_id', auth()->id())
            ->with('session')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('auto-edit-videos::library', [
            'renders' => $renders,
        ]);
    }

    public function store(StoreAutoEditSessionRequest $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        if (! $this->client->isConfigured()) {
            return back()->with(['type' => 'error', 'message' => __('Auto Edit Videos is not configured yet. Please contact the administrator.')]);
        }

        $result = $this->autoEdit->start($request->user(), $request->validated());

        if (is_string($result)) {
            return back()->withInput()->with(['type' => 'error', 'message' => $result]);
        }

        return to_route('dashboard.user.auto-edit-videos.sessions.show', $result)
            ->with(['type' => 'success', 'message' => __('Your edit has been sent to the editor.')]);
    }

    /**
     * Poll endpoint for the session list. Returns JSON session data so the
     * Alpine-driven UI can update state without HTML swapping.
     */
    public function check(Request $request): JsonResponse
    {
        $sessions = $this->sessionsFor(auth()->id())
            ->limit(30)
            ->get();

        $renders = AutoEditRender::query()
            ->where('user_id', auth()->id())
            ->with('session')
            ->when($request->filled('uuids'), fn ($query) => $query->whereIn('uuid', (array) $request->input('uuids')))
            ->latest('id')
            ->limit(24)
            ->get();

        return response()->json([
            'sessions' => $sessions->map(fn (AutoEditSession $session) => [
                'uuid'       => $session->uuid,
                'title'      => $session->displayTitle(),
                'status'     => $session->status->value,
                'label'      => $session->status->label(),
                'variant'    => $session->status->badgeVariant(),
                'terminal'   => $session->status->isTerminal(),
                'needs_user' => $session->status->needsUser(),
                'today'      => (bool) $session->created_at?->isToday(),
                'url'        => route('dashboard.user.auto-edit-videos.sessions.show', $session),
            ])->values(),
            'entries' => $renders->map(fn (AutoEditRender $render): array => $this->studioEntry($render))->values()->all(),
            'usage'   => $this->billing->usage(auth()->user()),
        ]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('This feature is disabled in demo mode.')], 422);
        }

        $deleted = AutoEditSession::query()
            ->where('user_id', auth()->id())
            ->whereIn('uuid', array_filter((array) $request->input('uuids', [])))
            ->get()
            ->each
            ->delete()
            ->count();

        return response()->json([
            'message' => trans_choice('{1} :count edit deleted.|[2,*] :count edits deleted.', $deleted, ['count' => $deleted]),
        ]);
    }

    /**
     * Studio card shape for the dashboard grid. Mirrors the VideoAgent grid
     * entries but is defined locally so this extension stays standalone.
     *
     * @return array<string, mixed>
     */
    protected function studioEntry(AutoEditRender $render): array
    {
        return [
            'source'             => 'auto-edit-videos',
            'source_label'       => __('Auto Edit Videos'),
            'id'                 => $render->uuid,
            'title'              => $render->displayTitle(),
            'thumb_url'          => $render->thumbnail_url,
            'video_url'          => $render->playbackUrl(),
            'status'             => $render->status->value,
            'error'              => $render->error_message,
            'status_url'         => route('dashboard.user.auto-edit-videos.check') . '?uuids[]=' . $render->uuid,
            'destroy_url'        => null,
            'session_url'        => $render->session ? route('dashboard.user.auto-edit-videos.sessions.show', $render->session) : null,
            'session_uuid'       => $render->session?->uuid,
            'created_at'         => $render->created_at?->diffForHumans(),
            'prompt'             => $render->session?->prompt,
            'model'              => $render->target_resolution,
            'formatted_duration' => $render->duration_seconds ? $render->formattedDuration() : null,
            '_ts'                => $render->created_at?->getTimestamp() ?? 0,
        ];
    }

    private function sessionsFor(int $userId): Builder
    {
        // The list only needs the render tally; a limited eager load would
        // apply its limit across the whole query rather than per session.
        return AutoEditSession::query()
            ->where('user_id', $userId)
            ->withCount('renders')
            ->latest('id');
    }
}
