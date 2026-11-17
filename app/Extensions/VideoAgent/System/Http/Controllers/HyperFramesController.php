<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Enums\VideoAgentKindEnum;
use App\Extensions\VideoAgent\System\Enums\VideoStatusEnum;
use App\Extensions\VideoAgent\System\Jobs\RefreshVideoAgentVideoJob;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use App\Extensions\VideoAgent\System\Services\VideoAgentBillingService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * HyperFrames is HeyGen's HTML-to-video framework — the same engine the Video
 * Agent composes with. Users upload a composition zip and we render it.
 */
class HyperFramesController extends Controller
{
    public function __construct(
        private readonly HeyGenClient $client,
        private readonly VideoAgentBillingService $billing,
    ) {}

    public function index(): View
    {
        return view('video-agent::hyperframes', [
            'renders' => VideoAgentVideo::query()
                ->where('user_id', auth()->id())
                ->where('kind', VideoAgentKindEnum::HyperFrames->value)
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        $data = $request->validate([
            'composition_url' => ['required', 'url', 'max:2048'],
            'title'           => ['nullable', 'string', 'max:191'],
            'fps'             => ['nullable', 'integer', 'min:24', 'max:60'],
            'resolution'      => ['nullable', 'string', 'in:720p,1080p,4k'],
        ]);

        $user = $request->user();

        $allowed = $this->billing->assertCanStart($user, VideoAgentKindEnum::HyperFrames, 60);

        if ($allowed !== true) {
            return back()->withInput()->with(['type' => 'error', 'message' => $allowed]);
        }

        $render = VideoAgentVideo::query()->create([
            'user_id' => $user->id,
            'team_id' => $user->team_id ?? null,
            'kind'    => VideoAgentKindEnum::HyperFrames,
            'title'   => $data['title'] ?? __('HyperFrames render'),
            'status'  => VideoStatusEnum::Queued,
            'payload' => $data,
        ]);

        $response = $this->client->createHyperFramesRender(array_filter([
            'composition' => ['type' => 'url', 'url' => $data['composition_url']],
            'fps'         => $data['fps'] ?? null,
            'resolution'  => $data['resolution'] ?? null,
        ], static fn ($value) => $value !== null));

        if (isset($response['error'])) {
            $render->forceFill([
                'status'        => VideoStatusEnum::Failed,
                'error_message' => $response['message'] ?? __('HeyGen rejected the render.'),
            ])->save();

            return back()->withInput()->with([
                'type'    => 'error',
                'message' => $response['message'] ?? __('HeyGen rejected the render.'),
            ]);
        }

        $payload = $response['data'] ?? $response;

        $render->forceFill([
            'heygen_id' => $payload['render_id'] ?? $payload['video_id'] ?? $payload['id'] ?? null,
            'status'    => VideoStatusEnum::fromApi($payload['status'] ?? null),
        ])->save();

        if (filled($render->heygen_id)) {
            RefreshVideoAgentVideoJob::dispatch($render->getKey());
        }

        return back()->with(['type' => 'success', 'message' => __('Render submitted.')]);
    }
}
