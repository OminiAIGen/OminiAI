<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Http\Controllers;

use App\Extensions\AutoEditVideos\System\Http\Requests\UpdateAutoEditVideosSettingsRequest;
use App\Extensions\AutoEditVideos\System\Services\Rendley\RendleyClient;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Services\Common\MenuService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AutoEditVideosSettingController extends Controller
{
    public function index(): View
    {
        return view('auto-edit-videos::settings.index');
    }

    public function update(UpdateAutoEditVideosSettingsRequest $request): RedirectResponse
    {
        if (Helper::appIsNotDemo()) {
            setting([
                'auto_edit_videos_api_key'         => (string) $request->input('auto_edit_videos_api_key', ''),
                'auto_edit_videos_agent_url'       => (string) $request->input('auto_edit_videos_agent_url', ''),
                'auto_edit_videos_public_base_url' => (string) $request->input('auto_edit_videos_public_base_url', ''),
            ])->save();

            // The sidebar entry is cached, and its visibility depends on these
            // settings, so it has to be rebuilt here.
            app(MenuService::class)->regenerate();
        }

        return back()->with([
            'type'    => 'success',
            'message' => __('Settings updated successfully.'),
        ]);
    }

    /**
     * Prove a key before anyone spends credits on it. `GET /v1/users/me` is the
     * cheapest authenticated call Rendley exposes.
     */
    public function testConnection(RendleyClient $client): JsonResponse
    {
        if (! $client->isConfigured()) {
            return response()->json(['message' => __('Save an API key first.')], 422);
        }

        $response = $client->me();

        if ($client->failed($response)) {
            return response()->json(['message' => $client->messageFrom($response)], 422);
        }

        return response()->json([
            'message' => __('Connected to Rendley as :email.', [
                'email' => $response['email'] ?? $response['id'] ?? __('your account'),
            ]),
        ]);
    }
}
