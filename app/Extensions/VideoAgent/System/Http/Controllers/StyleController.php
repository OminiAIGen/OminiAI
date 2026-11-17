<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Http\Controllers\Concerns\PaginatesCatalog;
use App\Extensions\VideoAgent\System\Models\VideoAgentStyle;
use App\Extensions\VideoAgent\System\Services\CatalogSyncService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Feeds the Look Pack picker modal.
 */
class StyleController extends Controller
{
    use PaginatesCatalog;

    public function __construct(private readonly CatalogSyncService $catalog) {}

    public function index(Request $request): JsonResponse
    {
        if (Helper::appIsNotDemo()) {
            $this->catalog->ensure(['styles']);
        }

        $styles = VideoAgentStyle::query()
            ->tagged($request->string('tag')->toString() ?: null)
            ->when(
                $request->string('q')->toString(),
                fn ($query, string $term) => $query->where('name', 'like', '%' . $term . '%'),
            )
            ->orderBy('name')
            ->paginate($this->pageSize($request));

        return $this->catalogResponse(
            $styles,
            fn (VideoAgentStyle $style): array => [
                'id'            => $style->heygen_id,
                'name'          => $style->name,
                'thumbnail'     => $style->thumbnail_url,
                'preview_video' => $style->preview_video_url,
                'aspect_ratio'  => $style->aspect_ratio,
                'tags'          => collect($style->tags ?? [])
                    ->map(fn ($tag): string => str((string) $tag)->headline()->toString())
                    ->all(),
            ],
            [
                'tags'    => collect(VideoAgentStyle::availableTags())
                    ->map(fn ($tag): array => [
                        'value' => $tag,
                        'label' => str((string) $tag)->headline()->toString(),
                    ])
                    ->all(),
                'syncing' => $this->catalog->isSyncing(['styles']),
            ],
        );
    }

    public function sync(): JsonResponse
    {
        $queued = $this->catalog->refresh(['styles']);

        return response()->json([
            'queued'  => $queued,
            'message' => $queued
                ? __('Refreshing the Look Pack catalog in the background.')
                : __('Look Pack catalog refreshed.'),
        ]);
    }
}
