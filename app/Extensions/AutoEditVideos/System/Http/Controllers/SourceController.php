<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Http\Controllers;

use App\Extensions\AutoEditVideos\System\Http\Requests\StoreAutoEditSourceRequest;
use App\Extensions\AutoEditVideos\System\Models\AutoEditSource;
use App\Extensions\AutoEditVideos\System\Services\SourceMediaService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SourceController extends Controller
{
    public function __construct(private readonly SourceMediaService $media) {}

    public function store(StoreAutoEditSourceRequest $request): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('This feature is disabled in demo mode.')], 422);
        }

        $result = $request->hasFile('file')
            ? $this->media->storeUpload($request->user(), $request->file('file'))
            : $this->media->storeUrl($request->user(), (string) $request->input('url'));

        if (is_string($result)) {
            return response()->json(['message' => $result], 422);
        }

        return response()->json([
            'source' => [
                'id'       => $result->id,
                'name'     => $result->displayName(),
                'type'     => $result->type->value,
                'duration' => $result->duration_seconds,
                'size'     => $result->size_bytes,
            ],
        ]);
    }

    public function destroy(AutoEditSource $source): JsonResponse
    {
        abort_unless($source->user_id === auth()->id(), 403);

        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('This feature is disabled in demo mode.')], 422);
        }

        if (filled($source->local_path)) {
            Storage::disk((string) config('auto-edit-videos.storage.disk', 'uploads'))->delete($source->local_path);
        }

        $source->delete();

        return response()->json(['message' => __('File removed.')]);
    }
}
