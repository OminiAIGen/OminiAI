<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Http\Controllers\Concerns\PaginatesCatalog;
use App\Extensions\VideoAgent\System\Http\Requests\StoreDigitalTwinRequest;
use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Services\CatalogSyncService;
use App\Extensions\VideoAgent\System\Services\DigitalTwinService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Feeds the avatar picker modal. There is no avatar page any more — the catalog
 * is only ever browsed from inside the composer.
 */
class AvatarController extends Controller
{
    use PaginatesCatalog;

    public function __construct(
        private readonly CatalogSyncService $catalog,
        private readonly DigitalTwinService $twins,
    ) {}

    public function index(Request $request): JsonResponse
    {
        if (Helper::appIsNotDemo()) {
            $this->catalog->ensure(['avatars']);
        }

        $ownership = $request->string('ownership')->toString();

        $query = VideoAgentAvatar::query()
            ->visibleTo($request->user()->id)
            ->ofType($request->string('avatar_type')->toString() ?: null)
            ->search($request->string('q')->toString() ?: null);

        if ($ownership === 'recent') {
            $recent = $this->recentlyUsed('avatar_id');

            // An empty IN() would match every row, which is the opposite of
            // "nothing used yet".
            $query->whereIn('heygen_id', $recent === [] ? [''] : $recent);
        } elseif ($ownership === 'private') {
            $query->where('user_id', $request->user()->id);
        } elseif ($ownership === 'public') {
            $query->where('ownership', 'public')->whereNull('user_id');
        }

        $avatars = $query->orderBy('name')->paginate($this->pageSize($request));

        return $this->catalogResponse(
            $avatars,
            fn (VideoAgentAvatar $avatar): array => $this->row($avatar),
            [
                'syncing'    => $this->catalog->isSyncing(['avatars']),
                'has_studio' => VideoAgentAvatar::query()->visibleTo($request->user()->id)->ofType('studio_avatar')->exists(),
            ],
        );
    }

    public function storeTwin(StoreDigitalTwinRequest $request): JsonResponse
    {
        abort_if(Helper::appIsDemo(), 403);

        $created = $this->twins->create(
            $request->user()->id,
            $request->string('name')->toString(),
            $request->string('asset_id')->toString(),
        );

        if ($created === null) {
            return response()->json(['message' => __('Could not create your Digital Twin. Please try again.')], 422);
        }

        return response()->json([
            'avatar' => $this->row($created),
        ], 201);
    }

    public function consent(VideoAgentAvatar $avatar): JsonResponse
    {
        abort_unless($avatar->user_id === auth()->id(), 404);

        $url = $this->twins->consentUrl($avatar);

        if ($url === null) {
            return response()->json(['message' => __('Could not start consent. Please try again.')], 422);
        }

        return response()->json(['url' => $url]);
    }

    public function refreshTwin(VideoAgentAvatar $avatar): JsonResponse
    {
        abort_unless($avatar->user_id === auth()->id(), 404);

        return response()->json(['avatar' => $this->row($this->twins->refresh($avatar))]);
    }

    /**
     * @return array<string, mixed>
     */
    private function row(VideoAgentAvatar $avatar): array
    {
        $needsConsent = $avatar->avatar_type === 'digital_twin'
            && $avatar->status === 'completed'
            && $avatar->consent_status !== 'accepted'
            && ! in_array($avatar->consent_status, ['failed', 'rejected'], true);

        $statusLabel = $avatar->isReady()
            ? null
            : match (true) {
                $avatar->status === 'failed', in_array($avatar->consent_status, ['failed', 'rejected'], true) => __('Failed'),
                $needsConsent                                                                                 => __('Awaiting consent'),
                default                                                                                       => __('Training'),
            };

        return [
            'id'            => $avatar->heygen_id,
            'name'          => $avatar->name ?: $avatar->heygen_id,
            'type'          => $avatar->avatar_type,
            'type_label'    => $avatar->avatar_type ? str($avatar->avatar_type)->headline()->toString() : null,
            'gender'        => $avatar->gender ? str($avatar->gender)->headline()->toString() : null,
            'ownership'     => $avatar->ownership,
            'preview_image' => $avatar->preview_image_url,
            'preview_video' => $avatar->preview_video_url,
            'voice_id'      => $avatar->default_voice_id,
            'ready'         => $avatar->isReady(),
            'failed'        => $avatar->status === 'failed' || in_array($avatar->consent_status, ['failed', 'rejected'], true),
            'needs_consent' => $needsConsent,
            'status_label'  => $statusLabel,
            'refresh_id'    => $avatar->user_id === auth()->id() ? $avatar->getKey() : null,
        ];
    }

    public function sync(): JsonResponse
    {
        $queued = $this->catalog->refresh(['avatars']);

        return response()->json([
            'queued'  => $queued,
            'message' => $queued
                ? __('Refreshing the avatar catalog in the background.')
                : __('Avatar catalog refreshed.'),
        ]);
    }
}
