<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers\Concerns;

use App\Extensions\VideoAgent\System\Models\VideoAgentSession;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The picker modals all consume one envelope and one infinite-scroll contract,
 * so the paging maths and the "recently used" lookup live here instead of being
 * copied into every catalog controller.
 */
trait PaginatesCatalog
{
    private const MAX_PAGE_SIZE = 60;

    protected function pageSize(Request $request, int $default = 24): int
    {
        return min(self::MAX_PAGE_SIZE, max(1, (int) $request->integer('page_size', $default)));
    }

    /**
     * @param  Closure(mixed): array<string, mixed>  $map
     * @param  array<string, mixed>  $extra
     */
    protected function catalogResponse(LengthAwarePaginator $paginator, Closure $map, array $extra = []): JsonResponse
    {
        return response()->json([
            'data'      => collect($paginator->items())->map($map)->values()->all(),
            'has_more'  => $paginator->hasMorePages(),
            'next_page' => $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null,
            'total'     => $paginator->total(),
        ] + $extra);
    }

    /**
     * The HeyGen ids this user has actually picked before, newest first. Derived
     * from their own sessions so no extra table is needed.
     *
     * @return array<int, string>
     */
    protected function recentlyUsed(string $column): array
    {
        if (! in_array($column, ['avatar_id', 'voice_id', 'style_id', 'brand_kit_id'], true)) {
            return [];
        }

        return VideoAgentSession::query()
            ->where('user_id', auth()->id())
            ->whereNotNull($column)
            ->latest()
            ->limit(50)
            ->pluck($column)
            ->unique()
            ->values()
            ->all();
    }
}
