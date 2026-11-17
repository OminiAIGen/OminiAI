<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Enums\VideoAgentKindEnum;
use App\Extensions\VideoAgent\System\Enums\VideoStatusEnum;
use App\Extensions\VideoAgent\System\Jobs\DownloadVideoAgentOutputJob;
use App\Extensions\VideoAgent\System\Jobs\RefreshVideoAgentVideoJob;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Models\VideoAgentVoice;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use App\Extensions\VideoAgent\System\Services\VideoAgentBillingService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Standalone text to speech through `POST /v3/voices/speech`. HeyGen may return
 * the audio synchronously or hand back an id to poll, so both are handled.
 */
class SpeechController extends Controller
{
    public function __construct(
        private readonly HeyGenClient $client,
        private readonly VideoAgentBillingService $billing,
    ) {}

    public function index(): View
    {
        return view('video-agent::speech', [
            'voices' => VideoAgentVoice::query()->orderBy('name')->limit(300)->get(),
            'clips'  => VideoAgentVideo::query()
                ->where('user_id', auth()->id())
                ->where('kind', VideoAgentKindEnum::Tts->value)
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
            'text'     => ['required', 'string', 'max:10000'],
            'voice_id' => ['required', 'string', 'max:191'],
            'speed'    => ['nullable', 'numeric', 'min:0.5', 'max:1.5'],
            'locale'   => ['nullable', 'string', 'max:32'],
        ]);

        $user = $request->user();
        $estimate = $this->billing->estimateSecondsFromPrompt($data['text']);

        $allowed = $this->billing->assertCanStart($user, VideoAgentKindEnum::Tts, $estimate);

        if ($allowed !== true) {
            return back()->withInput()->with(['type' => 'error', 'message' => $allowed]);
        }

        $clip = VideoAgentVideo::query()->create([
            'user_id' => $user->id,
            'team_id' => $user->team_id ?? null,
            'kind'    => VideoAgentKindEnum::Tts,
            'title'   => Str::limit($data['text'], 80),
            'status'  => VideoStatusEnum::Queued,
            'payload' => $data,
        ]);

        $response = $this->client->generateSpeech(array_filter([
            'text'     => $data['text'],
            'voice_id' => $data['voice_id'],
            'speed'    => $data['speed'] ?? null,
            'locale'   => $data['locale'] ?? null,
        ], static fn ($value) => $value !== null));

        if (isset($response['error'])) {
            $clip->forceFill([
                'status'        => VideoStatusEnum::Failed,
                'error_message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ])->save();

            return back()->withInput()->with([
                'type'    => 'error',
                'message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ]);
        }

        $payload = $response['data'] ?? $response;
        $audioUrl = $payload['audio_url'] ?? $payload['url'] ?? null;

        if (filled($audioUrl)) {
            $clip->forceFill([
                'status'           => VideoStatusEnum::Completed,
                'remote_url'       => $audioUrl,
                'duration_seconds' => isset($payload['duration']) ? (int) ceil((float) $payload['duration']) : $estimate,
                'payload'          => $payload,
            ])->save();

            DownloadVideoAgentOutputJob::dispatch($clip->getKey());
        } else {
            $clip->forceFill([
                'heygen_id' => $payload['audio_id'] ?? $payload['id'] ?? null,
                'status'    => VideoStatusEnum::fromApi($payload['status'] ?? null),
            ])->save();

            RefreshVideoAgentVideoJob::dispatch($clip->getKey());
        }

        return back()->with(['type' => 'success', 'message' => __('Your audio is being generated.')]);
    }
}
