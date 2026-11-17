<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Http\Controllers\Concerns\PaginatesCatalog;
use App\Extensions\VideoAgent\System\Models\VideoAgentVoice;
use App\Extensions\VideoAgent\System\Services\CatalogSyncService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Feeds the voice picker modal.
 */
class VoiceController extends Controller
{
    use PaginatesCatalog;

    public function __construct(private readonly CatalogSyncService $catalog) {}

    public function index(Request $request): JsonResponse
    {
        if (Helper::appIsNotDemo()) {
            $this->catalog->ensure(['voices']);
        }

        $type = $request->string('type')->toString();

        $query = VideoAgentVoice::query()
            ->inLanguage($request->string('language')->toString() ?: null)
            ->ofGender($request->string('gender')->toString() ?: null)
            ->search($request->string('q')->toString() ?: null);

        if ($type === 'recent') {
            $recent = $this->recentlyUsed('voice_id');

            $query->whereIn('heygen_id', $recent === [] ? [''] : $recent);
        } elseif ($type === 'private') {
            $query->where('type', '!=', 'public');
        } elseif ($type !== '') {
            $query->where('type', $type);
        }

        $voices = $query->orderBy('name')->paginate($this->pageSize($request, 30));

        return $this->catalogResponse(
            $voices,
            fn (VideoAgentVoice $voice): array => [
                'id'       => $voice->heygen_id,
                'name'     => $voice->name ?: $voice->heygen_id,
                'language' => $voice->language,
                'gender'   => $voice->gender ? str($voice->gender)->headline()->toString() : null,
                'type'     => $voice->type,
                'preview'  => $voice->preview_audio_url,
            ],
            [
                'languages' => VideoAgentVoice::availableLanguages(),
                'syncing'   => $this->catalog->isSyncing(['voices']),
            ],
        );
    }

    public function sync(): JsonResponse
    {
        $queued = $this->catalog->refresh(['voices']);

        return response()->json([
            'queued'  => $queued,
            'message' => $queued
                ? __('Refreshing the voice catalog in the background.')
                : __('Voice catalog refreshed.'),
        ]);
    }
}
