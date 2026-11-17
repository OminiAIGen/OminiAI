<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Services\Common\MenuService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VideoAgentSettingController extends Controller
{
    public const BALANCE_CACHE_KEY = 'video_agent_wallet_balance';

    public function __construct(private readonly HeyGenClient $client) {}

    public function index(Request $request): View
    {
        if ($request->boolean('refresh')) {
            Cache::forget(self::BALANCE_CACHE_KEY);
        }

        return view('video-agent::settings.index', [
            'account'          => $this->account(),
            'features'         => (array) config('video-agent.features', []),
            'webhookUrl'       => route('video-agent.webhook'),
            'webhookEndpoint'  => setting('video_agent_webhook_endpoint_id'),
            'hasWebhookSecret' => filled(setting('video_agent_webhook_secret')),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $features = array_keys((array) config('video-agent.features', []));

        $data = $request->validate([
            'video_agent_default_mode'        => ['required', 'string', 'in:generate,chat'],
            'video_agent_default_orientation' => ['nullable', 'string', 'in:landscape,portrait'],
            'video_agent_default_resolution'  => ['required', 'string', 'in:720p,1080p,4k'],
            'video_agent_poll_interval'       => ['required', 'integer', 'min:5', 'max:300'],
            'video_agent_enabled_features'    => ['nullable', 'array'],
            'video_agent_enabled_features.*'  => ['string', 'in:' . implode(',', $features)],
        ]);

        if (Helper::appIsNotDemo()) {
            setting([
                'video_agent_default_mode'        => $data['video_agent_default_mode'],
                'video_agent_default_orientation' => $data['video_agent_default_orientation'] ?? '',
                'video_agent_default_resolution'  => $data['video_agent_default_resolution'],
                'video_agent_poll_interval'       => (int) $data['video_agent_poll_interval'],
                'video_agent_enabled_features'    => json_encode(array_values($data['video_agent_enabled_features'] ?? [])),
            ])->save();

            Cache::forget(self::BALANCE_CACHE_KEY);

            app(MenuService::class)->regenerate();
        }

        return back()->with([
            'type'    => 'success',
            'message' => __('Updated Successfully'),
        ]);
    }

    /**
     * HeyGen shows the signing secret exactly once, at creation time.
     */
    public function registerWebhook(): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        $url = route('video-agent.webhook');

        if (! str_starts_with($url, 'https://')) {
            return back()->with([
                'type'    => 'error',
                'message' => __('HeyGen only accepts a publicly reachable HTTPS callback URL.'),
            ]);
        }

        $response = $this->client->createWebhookEndpoint($url);

        if (isset($response['error'])) {
            return back()->with([
                'type'    => 'error',
                'message' => $response['message'] ?? __('HeyGen rejected the webhook registration.'),
            ]);
        }

        $data = $response['data'] ?? $response;

        setting([
            'video_agent_webhook_endpoint_id' => $data['endpoint_id'] ?? $data['id'] ?? null,
            'video_agent_webhook_secret'      => $data['secret'] ?? null,
        ])->save();

        return back()->with(['type' => 'success', 'message' => __('Webhook endpoint registered.')]);
    }

    public function deleteWebhook(): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        $endpointId = setting('video_agent_webhook_endpoint_id');

        if (filled($endpointId)) {
            $this->client->deleteWebhookEndpoint((string) $endpointId);
        }

        setting([
            'video_agent_webhook_endpoint_id' => null,
            'video_agent_webhook_secret'      => null,
        ])->save();

        return back()->with(['type' => 'success', 'message' => __('Webhook endpoint removed.')]);
    }

    /**
     * @return array<string, mixed>
     */
    private function account(): array
    {
        if (! $this->client->isConfigured()) {
            return ['error' => 'not_configured'];
        }

        return Cache::remember(
            self::BALANCE_CACHE_KEY,
            now()->addMinutes(5),
            fn (): array => $this->client->currentUser(),
        );
    }
}
