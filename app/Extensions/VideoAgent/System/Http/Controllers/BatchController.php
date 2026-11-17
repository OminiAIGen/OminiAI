<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Enums\VideoAgentKindEnum;
use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Models\VideoAgentBatch;
use App\Extensions\VideoAgent\System\Models\VideoAgentVoice;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use App\Extensions\VideoAgent\System\Services\VideoAgentBillingService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Bulk generation. One row per script, submitted to HeyGen as a single batch so
 * the whole set can be tracked and polled with one bulk status call.
 */
class BatchController extends Controller
{
    public function __construct(
        private readonly HeyGenClient $client,
        private readonly VideoAgentBillingService $billing,
    ) {}

    public function index(): View
    {
        return view('video-agent::batches', [
            'batches' => VideoAgentBatch::query()
                ->where('user_id', auth()->id())
                ->latest()
                ->limit(20)
                ->get(),
            'avatars' => VideoAgentAvatar::query()->visibleTo(auth()->id())->ready()->orderBy('name')->limit(200)->get(),
            'voices'  => VideoAgentVoice::query()->orderBy('name')->limit(300)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        $data = $request->validate([
            'avatar_id' => ['required', 'string', 'max:191', VideoAgentAvatar::selectionRule($request->user()->id)],
            'voice_id'  => ['required', 'string', 'max:191'],
            'scripts'   => ['required', 'string', 'max:100000'],
        ]);

        $scripts = collect(preg_split('/\R{2,}/', trim($data['scripts'])) ?: [])
            ->map(static fn (string $script): string => trim($script))
            ->filter()
            ->values();

        if ($scripts->isEmpty()) {
            return back()->withInput()->withErrors(['scripts' => __('Add at least one script, separated by a blank line.')]);
        }

        if ($scripts->count() > 100) {
            return back()->withInput()->withErrors(['scripts' => __('A batch can hold at most 100 videos.')]);
        }

        $user = $request->user();
        $estimate = $scripts->sum(fn (string $script): int => $this->billing->estimateSecondsFromPrompt($script));

        $allowed = $this->billing->assertCanStart($user, VideoAgentKindEnum::Avatar, (int) $estimate);

        if ($allowed !== true) {
            return back()->withInput()->with(['type' => 'error', 'message' => $allowed]);
        }

        $items = $scripts->map(fn (string $script): array => [
            'type'      => 'avatar',
            'avatar_id' => $data['avatar_id'],
            'voice_id'  => $data['voice_id'],
            'script'    => $script,
        ])->all();

        $batch = VideoAgentBatch::query()->create([
            'user_id'    => $user->id,
            'kind'       => VideoAgentBatch::KIND_VIDEO,
            'item_count' => count($items),
            'payload'    => ['items' => $items],
        ]);

        $response = $this->client->createVideoBatch(['videos' => $items]);

        if (isset($response['error'])) {
            $batch->forceFill([
                'status'        => 'failed',
                'error_message' => $response['message'] ?? __('HeyGen rejected the batch.'),
            ])->save();

            return back()->withInput()->with([
                'type'    => 'error',
                'message' => $response['message'] ?? __('HeyGen rejected the batch.'),
            ]);
        }

        $payload = $response['data'] ?? $response;

        $batch->forceFill([
            'heygen_id' => $payload['batch_id'] ?? $payload['id'] ?? null,
            'status'    => $payload['status'] ?? 'processing',
            'payload'   => array_merge($batch->payload ?? [], ['response' => $payload]),
        ])->save();

        return back()->with(['type' => 'success', 'message' => __('Batch submitted.')]);
    }

    public function refresh(VideoAgentBatch $batch): RedirectResponse
    {
        abort_unless($batch->user_id === auth()->id(), 403);

        if (blank($batch->heygen_id)) {
            return back();
        }

        $response = $this->client->getVideoBatch((string) $batch->heygen_id);

        $batch->forceFill(['last_polled_at' => now()])->save();

        if (isset($response['error'])) {
            return back()->with(['type' => 'error', 'message' => $response['message'] ?? __('Could not refresh the batch.')]);
        }

        $data = $response['data'] ?? $response;

        $batch->forceFill([
            'status'          => $data['status'] ?? $batch->status,
            'completed_count' => (int) ($data['completed_count'] ?? $batch->completed_count),
            'payload'         => array_merge($batch->payload ?? [], ['latest' => $data]),
        ])->save();

        return back();
    }
}
