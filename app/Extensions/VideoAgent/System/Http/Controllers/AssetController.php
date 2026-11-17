<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Http\Requests\StoreVideoAgentAssetRequest;
use App\Extensions\VideoAgent\System\Models\VideoAgentAsset;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AssetController extends Controller
{
    public function __construct(private readonly HeyGenClient $client) {}

    public function store(StoreVideoAgentAssetRequest $request): JsonResponse
    {
        $file = $request->file('file');

        $response = $this->client->uploadAsset(
            (string) $file->get(),
            $file->getClientOriginalName(),
            (string) $file->getMimeType(),
        );

        if (isset($response['error'])) {
            return response()->json([
                'message' => $response['message'] ?? __('The upload failed.'),
            ], 422);
        }

        $data = $response['data'] ?? $response;
        $assetId = $data['asset_id'] ?? $data['id'] ?? null;

        if (blank($assetId)) {
            return response()->json(['message' => __('HeyGen did not return an asset id.')], 422);
        }

        $asset = VideoAgentAsset::query()->updateOrCreate(
            [
                'user_id'         => $request->user()->id,
                'heygen_asset_id' => (string) $assetId,
            ],
            [
                'name'       => $file->getClientOriginalName(),
                'url'        => $data['url'] ?? null,
                'mime_type'  => $data['mime_type'] ?? $file->getMimeType(),
                'size_bytes' => $data['size_bytes'] ?? $file->getSize(),
            ],
        );

        return response()->json([
            'asset_id' => $asset->heygen_asset_id,
            'name'     => $asset->name,
            'url'      => $asset->url,
            'is_image' => $asset->isImage(),
        ]);
    }

    public function destroy(VideoAgentAsset $asset): JsonResponse
    {
        abort_unless($asset->user_id === auth()->id(), 403);

        $this->client->deleteAsset((string) $asset->heygen_asset_id);
        $asset->delete();

        return response()->json(['deleted' => true]);
    }
}
