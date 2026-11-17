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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Proofread is a three-step round trip: create a session from a video, edit the
 * SRT HeyGen extracts, then regenerate the video from the corrected subtitles.
 * The session lives on a `proofread` row in the library.
 *
 * HeyGen files this under video translation, and the shapes follow from that:
 * creating one needs a target language and answers with `proofread_ids` (one
 * per language), the SRT is exchanged as a file rather than as text, and the
 * final render is a `video_translation_id`, not a video id.
 */
class ProofreadController extends Controller
{
    public function __construct(
        private readonly HeyGenClient $client,
        private readonly VideoAgentBillingService $billing,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        // `output_languages` is required by HeyGen even when the target matches
        // the source: proofreading is a translation job with one language.
        $data = $request->validate([
            'video_url'       => ['required', 'url', 'max:2048'],
            'output_language' => ['required', 'string', 'max:64'],
        ]);

        $user = $request->user();

        $allowed = $this->billing->assertCanStart($user, VideoAgentKindEnum::Proofread, 60);

        if ($allowed !== true) {
            return back()->withInput()->with(['type' => 'error', 'message' => $allowed]);
        }

        $response = $this->client->createProofreadSession([
            'video'            => ['type' => 'url', 'url' => $data['video_url']],
            'output_languages' => [$data['output_language']],
            'title'            => Str::limit($data['video_url'], 80),
            'mode'             => 'precision',
        ]);

        if (isset($response['error'])) {
            return back()->withInput()->with([
                'type'    => 'error',
                'message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ]);
        }

        $payload = $response['data'] ?? $response;
        $sessionId = $payload['proofread_ids'][0] ?? null;

        if (blank($sessionId)) {
            return back()->with(['type' => 'error', 'message' => __('HeyGen did not return a proofread session id.')]);
        }

        $job = VideoAgentVideo::query()->create([
            'user_id' => $user->id,
            'team_id' => $user->team_id ?? null,
            'kind'    => VideoAgentKindEnum::Proofread,
            'title'   => Str::limit($data['video_url'], 80),
            'status'  => VideoStatusEnum::Processing,
            'payload' => [
                'proofread_session_id' => $sessionId,
                'video_url'            => $data['video_url'],
                'output_language'      => $data['output_language'],
            ],
        ]);

        return to_route('dashboard.user.video-agent.proofread.show', $job)
            ->with(['type' => 'success', 'message' => __('Proofread session created.')]);
    }

    public function show(VideoAgentVideo $video): View
    {
        $this->authorizeJob($video);

        return view('video-agent::proofread', [
            'job' => $video,
            'srt' => $this->currentSrt($video),
        ]);
    }

    public function update(Request $request, VideoAgentVideo $video): RedirectResponse
    {
        $this->authorizeJob($video);

        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        $data = $request->validate([
            'srt' => ['required', 'string', 'max:200000'],
        ]);

        // HeyGen takes the subtitles as a file reference, never as a string, so
        // the edited text is uploaded as an asset first and handed over by id.
        $asset = $this->client->uploadAsset($data['srt'], 'proofread.srt', 'application/x-subrip');

        if (isset($asset['error'])) {
            return back()->withInput()->with([
                'type'    => 'error',
                'message' => $asset['message'] ?? __('The subtitles could not be uploaded.'),
            ]);
        }

        $assetPayload = $asset['data'] ?? $asset;
        $assetId = $assetPayload['asset_id'] ?? $assetPayload['id'] ?? null;

        if (blank($assetId)) {
            return back()->withInput()->with([
                'type'    => 'error',
                'message' => __('The subtitles could not be uploaded.'),
            ]);
        }

        $response = $this->client->uploadProofreadSrt($this->sessionId($video), [
            'srt' => ['type' => 'asset_id', 'asset_id' => (string) $assetId],
        ]);

        if (isset($response['error'])) {
            return back()->withInput()->with([
                'type'    => 'error',
                'message' => $response['message'] ?? __('HeyGen rejected the subtitles.'),
            ]);
        }

        return back()->with(['type' => 'success', 'message' => __('Subtitles saved.')]);
    }

    public function generate(VideoAgentVideo $video): RedirectResponse
    {
        $this->authorizeJob($video);

        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        $response = $this->client->generateVideoFromProofread($this->sessionId($video));

        if (isset($response['error'])) {
            return back()->with([
                'type'    => 'error',
                'message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ]);
        }

        $payload = $response['data'] ?? $response;

        // What comes back is a translation id; `syncVideo()` reads the kind to
        // decide which endpoint to poll.
        $video->forceFill([
            'heygen_id' => $payload['video_translation_id'] ?? $video->heygen_id,
            'status'    => VideoStatusEnum::fromApi($payload['status'] ?? null),
        ])->save();

        if (filled($video->heygen_id)) {
            RefreshVideoAgentVideoJob::dispatch($video->getKey());
        }

        return to_route('dashboard.user.video-agent.index', ['library' => 1])
            ->with(['type' => 'success', 'message' => __('Your video is rendering.')]);
    }

    /**
     * The SRT endpoint hands back presigned URLs, not subtitle text, so the
     * edited file has to be fetched before it can be shown in the editor.
     */
    private function currentSrt(VideoAgentVideo $video): string
    {
        $response = $this->client->downloadProofreadSrt($this->sessionId($video));

        if (isset($response['error'])) {
            return '';
        }

        $payload = $response['data'] ?? $response;
        $url = $payload['srt_url'] ?? $payload['original_srt_url'] ?? null;

        if (! is_string($url) || blank($url)) {
            return '';
        }

        try {
            $file = Http::timeout(20)->get($url);

            return $file->successful() ? $file->body() : '';
        } catch (Throwable) {
            return '';
        }
    }

    private function sessionId(VideoAgentVideo $video): string
    {
        return (string) ($video->payload['proofread_session_id'] ?? '');
    }

    private function authorizeJob(VideoAgentVideo $video): void
    {
        abort_unless($video->user_id === auth()->id(), 403);
        abort_unless($video->kind === VideoAgentKindEnum::Proofread, 404);
    }
}
