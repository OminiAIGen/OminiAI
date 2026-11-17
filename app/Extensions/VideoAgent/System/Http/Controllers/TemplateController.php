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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Feeds the template picker modal: a grid of Studio templates, then the
 * variable form for whichever one the user opens.
 */
class TemplateController extends Controller
{
    private const CACHE_KEY = 'video_agent_templates';

    public function __construct(
        private readonly HeyGenClient $client,
        private readonly VideoAgentBillingService $billing,
    ) {}

    public function index(Request $request): JsonResponse
    {
        if ($request->boolean('refresh')) {
            Cache::forget(self::CACHE_KEY);
        }

        $templates = collect($this->templates())
            ->map(function (array $template): ?array {
                $id = $template['template_id'] ?? $template['id'] ?? null;

                return blank($id) ? null : [
                    'id'          => (string) $id,
                    'name'        => $template['name'] ?? $id,
                    'description' => $template['description'] ?? null,
                    'thumbnail'   => $template['thumbnail_url'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();

        return response()->json([
            'data'      => $templates,
            'has_more'  => false,
            'next_page' => null,
            'total'     => count($templates),
        ]);
    }

    public function show(string $template): JsonResponse
    {
        $response = $this->client->getTemplate($template);

        if (isset($response['error'])) {
            return response()->json([
                'message' => $response['message'] ?? __('This template could not be loaded.'),
            ], 404);
        }

        $data = $response['data'] ?? $response;

        return response()->json([
            'id'        => $data['template_id'] ?? $data['id'] ?? $template,
            'name'      => $data['name'] ?? $template,
            'thumbnail' => $data['thumbnail_url'] ?? null,
            'variables' => collect($this->variablesFrom($data))
                ->map(fn (string $variable): array => [
                    'name'  => $variable,
                    'label' => Str::headline($variable),
                ])
                ->all(),
        ]);
    }

    public function generate(Request $request, string $template): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        // A template takes a brand glossary, never a brand kit: the look is
        // baked into the template itself, so only the pronunciation of the
        // speech it generates is still up for grabs.
        $data = $request->validate([
            'title'             => ['nullable', 'string', 'max:191'],
            'variables'         => ['nullable', 'array'],
            'variables.*'       => ['nullable', 'string', 'max:5000'],
            'caption'           => ['nullable', 'boolean'],
            'brand_glossary_id' => ['nullable', 'string', 'max:64'],
        ]);

        $user = $request->user();
        $variables = array_filter($data['variables'] ?? [], static fn ($value) => $value !== null && $value !== '');
        $estimate = $this->billing->estimateSecondsFromPrompt(implode(' ', $variables) ?: 'template');

        $allowed = $this->billing->assertCanStart($user, VideoAgentKindEnum::Template, $estimate);

        if ($allowed !== true) {
            return back()->withInput()->with(['type' => 'error', 'message' => $allowed]);
        }

        $video = VideoAgentVideo::query()->create([
            'user_id' => $user->id,
            'team_id' => $user->team_id ?? null,
            'kind'    => VideoAgentKindEnum::Template,
            'title'   => $data['title'] ?? Str::limit((string) reset($variables), 80) ?: __('Template Video'),
            'status'  => VideoStatusEnum::Queued,
            'payload' => [
                'template_id'       => $template,
                'variables'         => $variables,
                'brand_glossary_id' => $data['brand_glossary_id'] ?? null,
            ],
        ]);

        $response = $this->client->generateFromTemplate($template, array_filter([
            'variables'         => $variables ?: null,
            'title'             => $data['title'] ?? null,
            'caption'           => $data['caption'] ?? null,
            'brand_glossary_id' => $data['brand_glossary_id'] ?? null,
        ], static fn ($value) => $value !== null && $value !== ''));

        if (isset($response['error'])) {
            $video->forceFill([
                'status'        => VideoStatusEnum::Failed,
                'error_message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ])->save();

            return back()->withInput()->with([
                'type'    => 'error',
                'message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ]);
        }

        $payload = $response['data'] ?? $response;

        $video->forceFill([
            'heygen_id' => $payload['video_id'] ?? null,
            'status'    => VideoStatusEnum::fromApi($payload['status'] ?? null),
        ])->save();

        RefreshVideoAgentVideoJob::dispatch($video->getKey());

        return to_route('dashboard.user.video-agent.index', ['library' => 1])
            ->with(['type' => 'success', 'message' => __('Your video is rendering.')]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function templates(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            now()->addMinutes(30),
            fn (): array => $this->client->listTemplates(),
        );
    }

    /**
     * HeyGen has published template variables under a couple of shapes; accept
     * either a keyed map or a plain list of names.
     *
     * @param  array<string, mixed>  $template
     *
     * @return array<int, string>
     */
    private function variablesFrom(array $template): array
    {
        $variables = $template['variables'] ?? $template['variable_names'] ?? [];

        if (! is_array($variables)) {
            return [];
        }

        return collect($variables)
            ->map(fn ($value, $key): string => is_string($key) ? $key : (string) ($value['name'] ?? $value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
