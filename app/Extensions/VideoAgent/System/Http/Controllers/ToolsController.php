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
use Illuminate\Support\Str;

/**
 * Post-production: lipsync, AI clipping, filler-word removal, proofread and
 * (when the admin enables it) video translation.
 */
class ToolsController extends Controller
{
    public function __construct(
        private readonly HeyGenClient $client,
        private readonly VideoAgentBillingService $billing,
    ) {}

    public function index(): View
    {
        return view('video-agent::tools', [
            'features'  => (array) config('video-agent.features', []),
            // Proofread needs the same list: HeyGen treats it as a translation
            // job and rejects a session with no target language.
            'languages' => $this->enabled('translation') || $this->enabled('proofread')
                ? $this->client->listTranslationLanguages()
                : [],
            'jobs'      => VideoAgentVideo::query()
                ->where('user_id', auth()->id())
                ->whereIn('kind', [
                    VideoAgentKindEnum::Lipsync->value,
                    VideoAgentKindEnum::Clip->value,
                    VideoAgentKindEnum::Filler->value,
                    VideoAgentKindEnum::Proofread->value,
                    VideoAgentKindEnum::Translation->value,
                ])
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }

    public function lipsync(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'video_url' => ['required', 'url', 'max:2048'],
            'audio_url' => ['nullable', 'url', 'max:2048'],
            'script'    => ['nullable', 'string', 'max:10000'],
            'voice_id'  => ['nullable', 'string', 'max:191'],
            'mode'      => ['nullable', 'string', 'in:speed,precision'],
        ]);

        return $this->submit(
            $request,
            VideoAgentKindEnum::Lipsync,
            'lipsync',
            fn (): array => $this->client->createLipsync(array_filter([
                'video'    => ['type' => 'url', 'url' => $data['video_url']],
                'audio'    => filled($data['audio_url'] ?? null) ? ['type' => 'url', 'url' => $data['audio_url']] : null,
                'script'   => $data['script'] ?? null,
                'voice_id' => $data['voice_id'] ?? null,
                'mode'     => $data['mode'] ?? 'speed',
            ], static fn ($value) => $value !== null)),
            $data,
            Str::limit($data['video_url'], 80),
        );
    }

    public function clip(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'video_url'  => ['required', 'url', 'max:2048'],
            'clip_count' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        return $this->submit(
            $request,
            VideoAgentKindEnum::Clip,
            'clipping',
            fn (): array => $this->client->createAiClipping(array_filter([
                'video'      => ['type' => 'url', 'url' => $data['video_url']],
                'clip_count' => $data['clip_count'] ?? null,
            ], static fn ($value) => $value !== null)),
            $data,
            __('AI clips'),
        );
    }

    public function fillerWords(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'video_url' => ['required', 'url', 'max:2048'],
        ]);

        return $this->submit(
            $request,
            VideoAgentKindEnum::Filler,
            'filler',
            fn (): array => $this->client->createFillerWordRemoval([
                'video' => ['type' => 'url', 'url' => $data['video_url']],
            ]),
            $data,
            __('Filler word removal'),
        );
    }

    public function translate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'video_url'          => ['required', 'url', 'max:2048'],
            'output_languages'   => ['required', 'array', 'min:1'],
            'output_languages.*' => ['string', 'max:64'],
            'mode'               => ['nullable', 'string', 'in:speed,precision'],
        ]);

        return $this->submit(
            $request,
            VideoAgentKindEnum::Translation,
            'translation',
            fn (): array => $this->client->createVideoTranslation(array_filter([
                'video'            => ['type' => 'url', 'url' => $data['video_url']],
                'output_languages' => $data['output_languages'],
                'mode'             => $data['mode'] ?? 'speed',
            ], static fn ($value) => $value !== null)),
            $data,
            __('Translation'),
        );
    }

    /**
     * Every tool follows the same shape: gate on the feature flag and the
     * billing rules, create a queued row, call HeyGen, then poll it.
     *
     * @param  callable(): array<string, mixed>  $call
     * @param  array<string, mixed>  $payload
     */
    private function submit(
        Request $request,
        VideoAgentKindEnum $kind,
        string $feature,
        callable $call,
        array $payload,
        string $title,
    ): RedirectResponse {
        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        if (! $this->enabled($feature)) {
            return back()->with(['type' => 'error', 'message' => __('This tool is disabled.')]);
        }

        $user = $request->user();
        $estimate = 60;

        $allowed = $this->billing->assertCanStart($user, $kind, $estimate);

        if ($allowed !== true) {
            return back()->withInput()->with(['type' => 'error', 'message' => $allowed]);
        }

        $job = VideoAgentVideo::query()->create([
            'user_id' => $user->id,
            'team_id' => $user->team_id ?? null,
            'kind'    => $kind,
            'title'   => $title,
            'status'  => VideoStatusEnum::Queued,
            'payload' => $payload,
        ]);

        $response = $call();

        if (isset($response['error'])) {
            $job->forceFill([
                'status'        => VideoStatusEnum::Failed,
                'error_message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ])->save();

            return back()->withInput()->with([
                'type'    => 'error',
                'message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ]);
        }

        $data = $response['data'] ?? $response;

        $job->forceFill([
            'heygen_id' => $this->idFrom($data),
            'status'    => VideoStatusEnum::fromApi($data['status'] ?? null),
        ])->save();

        if (filled($job->heygen_id)) {
            RefreshVideoAgentVideoJob::dispatch($job->getKey());
        }

        return back()->with(['type' => 'success', 'message' => __('Submitted. It will appear in your library when it is ready.')]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function idFrom(array $data): ?string
    {
        $id = $data['video_id']
            ?? $data['lipsync_id']
            ?? $data['ai_clipping_id']
            ?? $data['filler_word_removal_id']
            ?? $data['clipping_id']
            ?? $data['id']
            ?? ($data['video_translation_ids'][0] ?? null);

        return filled($id) ? (string) $id : null;
    }

    private function enabled(string $feature): bool
    {
        $stored = json_decode((string) setting('video_agent_enabled_features', 'null'), true);

        if (is_array($stored)) {
            return in_array($feature, $stored, true);
        }

        return (bool) config("video-agent.features.{$feature}", false);
    }
}
