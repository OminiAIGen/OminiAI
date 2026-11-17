<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Http\Requests\StoreVideoRequest;
use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Models\VideoAgentVoice;
use App\Extensions\VideoAgent\System\Services\CatalogSyncService;
use App\Extensions\VideoAgent\System\Services\VideoAgentBillingService;
use App\Extensions\VideoAgent\System\Services\VideoCreationService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class VideoController extends Controller
{
    public function __construct(
        private readonly VideoCreationService $videos,
        private readonly VideoAgentBillingService $billing,
        private readonly CatalogSyncService $catalog,
    ) {}

    public function create(): View
    {
        $this->catalog->ensure(['avatars', 'voices']);

        return view('video-agent::create', [
            'catalogSyncing' => $this->catalog->isSyncing(['avatars', 'voices']),
            'avatars'        => VideoAgentAvatar::query()
                ->visibleTo(auth()->id())
                ->ready()
                ->select(['heygen_id', 'name', 'default_voice_id', 'preview_image_url'])
                ->orderBy('name')
                ->limit(200)
                ->get(),
            'voices'         => VideoAgentVoice::query()
                ->select(['heygen_id', 'name', 'language'])
                ->orderBy('name')
                ->limit(300)
                ->get(),
            'usage'          => $this->billing->usage(auth()->user()),
        ]);
    }

    public function store(StoreVideoRequest $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        $video = $this->videos->create($request->user(), $request->validated());

        if (is_string($video)) {
            return back()->withInput()->with(['type' => 'error', 'message' => $video]);
        }

        return to_route('dashboard.user.video-agent.index', ['library' => 1])
            ->with(['type' => 'success', 'message' => __('Your video is rendering.')]);
    }
}
