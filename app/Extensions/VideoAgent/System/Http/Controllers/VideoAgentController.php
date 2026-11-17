<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Enums\SessionModeEnum;
use App\Extensions\VideoAgent\System\Enums\VideoAgentKindEnum;
use App\Extensions\VideoAgent\System\Enums\VideoStatusEnum;
use App\Extensions\VideoAgent\System\Http\Controllers\Concerns\PaginatesCatalog;
use App\Extensions\VideoAgent\System\Http\Controllers\Concerns\PresentsVideoAgent;
use App\Extensions\VideoAgent\System\Http\Requests\StoreVideoAgentSessionRequest;
use App\Extensions\VideoAgent\System\Models\VideoAgentAsset;
use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Models\VideoAgentBrandKit;
use App\Extensions\VideoAgent\System\Models\VideoAgentSession;
use App\Extensions\VideoAgent\System\Models\VideoAgentStyle;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Models\VideoAgentVoice;
use App\Extensions\VideoAgent\System\Services\VideoAgentBillingService;
use App\Extensions\VideoAgent\System\Services\VideoAgentService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VideoAgentController extends Controller
{
    use PaginatesCatalog;
    use PresentsVideoAgent;

    public function __construct(
        private readonly VideoAgentService $service,
        private readonly VideoAgentBillingService $billing,
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        $videos = VideoAgentVideo::query()
            ->where('user_id', $user->id)
            ->visibleToUser()
            ->with('session')
            ->latest()
            ->limit(24)
            ->get()
            ->map(fn (VideoAgentVideo $video): array => $this->studioEntry($video));

        $latestSession = VideoAgentSession::query()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return view('video-agent::index', [
            'userName'         => explode(' ', (string) ($user->name ?? ''))[0],
            'sessionsCount'    => VideoAgentSession::query()->where('user_id', $user->id)->count(),
            'latestSessionUrl' => $latestSession ? route('dashboard.user.video-agent.sessions.show', $latestSession) : null,
            'todayEntries'     => $videos->filter(fn (array $entry): bool => (bool) ($entry['_ts'] && Carbon::createFromTimestamp($entry['_ts'])->isToday()))->values()->all(),
            'previousEntries'  => $videos->reject(fn (array $entry): bool => (bool) ($entry['_ts'] && Carbon::createFromTimestamp($entry['_ts'])->isToday()))->values()->all(),
            'usage'            => $this->billing->usage($user),
            'sessions'         => VideoAgentSession::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(12)
                ->get(),
            'brandKits'          => VideoAgentBrandKit::query()->where('type', 'kit')->orderBy('name')->get(),
            'modes'              => SessionModeEnum::cases(),
            'defaultMode'        => setting('video_agent_default_mode', config('video-agent.defaults.mode')),
            'defaultOrientation' => setting('video_agent_default_orientation', config('video-agent.defaults.orientation')),
            'durations'          => (array) config('video-agent.durations', [15, 30, 60]),
        ]);
    }

    /**
     * Feeds the library picker modal.
     */
    public function library(Request $request): JsonResponse
    {
        $videos = VideoAgentVideo::query()
            ->where('user_id', auth()->id())
            ->visibleToUser()
            ->when($request->filled('kind'), fn ($query) => $query->where('kind', $request->string('kind')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when(
                $request->string('q')->toString(),
                fn ($query, string $term) => $query->where('title', 'like', '%' . $term . '%'),
            )
            ->latest()
            ->paginate($this->pageSize($request, 12));

        return $this->catalogResponse(
            $videos,
            fn (VideoAgentVideo $video): array => $this->videoRow($video),
            [
                'kinds'    => collect(VideoAgentKindEnum::cases())
                    ->map(fn (VideoAgentKindEnum $kind): array => ['value' => $kind->value, 'label' => $kind->label()])
                    ->all(),
                'statuses' => collect(VideoStatusEnum::cases())
                    ->map(fn (VideoStatusEnum $status): array => ['value' => $status->value, 'label' => $status->label()])
                    ->all(),
            ],
        );
    }

    public function store(StoreVideoAgentSessionRequest $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->withInput()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        $session = $this->service->start($request->user(), [
            'prompt'       => $request->string('prompt')->toString(),
            'mode'         => $request->input('mode'),
            'style_id'     => $request->input('style_id'),
            'brand_kit_id' => $request->input('brand_kit_id'),
            'avatar_id'    => $request->input('avatar_id'),
            'voice_id'     => $request->input('voice_id'),
            'orientation'  => $request->input('orientation'),
            'duration'     => $request->input('duration'),
            'instructions' => $request->input('instructions'),
            'files'        => $this->filesFor($request),
        ]);

        if (is_string($session)) {
            return back()->withInput()->with(['type' => 'error', 'message' => $session]);
        }

        return to_route('dashboard.user.video-agent.sessions.show', $session);
    }

    /**
     * Polled by the landing page so sessions and renders move without a manual
     * refresh. Also used by the session rail.
     */
    public function check(Request $request): JsonResponse
    {
        $user = auth()->user();

        $videos = VideoAgentVideo::query()
            ->where('user_id', $user->id)
            ->visibleToUser()
            ->with('session')
            ->when($request->filled('uuids'), fn ($query) => $query->whereIn('uuid', (array) $request->input('uuids')))
            ->latest()
            ->limit(24)
            ->get();

        return response()->json([
            'usage'    => $this->billing->usage($user),
            'sessions' => VideoAgentSession::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(24)
                ->get()
                ->map(fn (VideoAgentSession $session): array => $this->sessionRow($session))
                ->all(),
            'videos'  => $videos->map(fn (VideoAgentVideo $video): array => $this->videoRow($video))->all(),
            'entries' => $videos->map(fn (VideoAgentVideo $video): array => $this->studioEntry($video))->all(),
        ]);
    }

    public function destroy(Request $request, VideoAgentVideo $video): RedirectResponse|JsonResponse
    {
        abort_unless($video->user_id === auth()->id(), 403);

        $video->delete();

        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with(['type' => 'success', 'message' => __('Deleted successfully.')]);
    }

    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'uuids'   => ['required', 'array'],
            'uuids.*' => ['string'],
        ]);

        $deleted = VideoAgentVideo::query()
            ->where('user_id', auth()->id())
            ->whereIn('uuid', $request->input('uuids'))
            ->delete();

        if ($request->wantsJson()) {
            return response()->json(['deleted' => $deleted]);
        }

        return back()->with(['type' => 'success', 'message' => __('Deleted successfully.')]);
    }

    /**
     * The avatar, voice, Look Pack and brand kit the composer pills should show
     * on first paint when the form is redisplayed after a validation error.
     *
     * @return array<string, array{id: string, name: string, preview: ?string}|null>
     */
    public static function pillSelections(): array
    {
        // On a first paint there is nothing to restore, so don't run four
        // lookups for values that are not there.
        return [
            'avatar' => self::pill(VideoAgentAvatar::class, 'avatar_id', 'preview_image_url'),
            'voice'  => self::pill(VideoAgentVoice::class, 'voice_id', null),
            'style'  => self::pill(VideoAgentStyle::class, 'style_id', 'thumbnail_url'),
            'brand'  => self::pill(VideoAgentBrandKit::class, 'brand_kit_id', null),
        ];
    }

    /**
     * The pill paints an <img>, so this column must be an image. A voice has
     * only an audio sample, which is why it passes null.
     *
     * @return array{id: string, name: string, thumbnail: ?string}|null
     */
    private static function pill(string $model, string $field, ?string $thumbnailColumn): ?array
    {
        $id = old($field);

        if (blank($id)) {
            return null;
        }

        $query = $model::query();

        if ($model === VideoAgentAvatar::class) {
            $query->visibleTo(auth()->id())->ready();
        }

        $record = $query->firstWhere('heygen_id', $id);

        if ($record === null) {
            return null;
        }

        return [
            'id'        => (string) $record->heygen_id,
            'name'      => (string) ($record->name ?: $record->heygen_id),
            'thumbnail' => $thumbnailColumn ? $record->{$thumbnailColumn} : null,
        ];
    }

    /**
     * Turn the selected uploads into the `files` union HeyGen expects.
     *
     * @return array<int, array{type: string, asset_id: string}>
     */
    private function filesFor(StoreVideoAgentSessionRequest $request): array
    {
        $assetIds = (array) $request->input('asset_ids', []);

        if ($assetIds === []) {
            return [];
        }

        return VideoAgentAsset::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('heygen_asset_id', $assetIds)
            ->get()
            ->map(fn (VideoAgentAsset $asset): array => [
                'type'     => 'asset_id',
                'asset_id' => $asset->heygen_asset_id,
            ])
            ->all();
    }
}
