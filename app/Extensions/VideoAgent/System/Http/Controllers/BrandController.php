<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Models\VideoAgentBrandKit;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Feeds the brand picker modal: kits the agent can apply, plus the glossaries
 * that keep product names spoken the way the customer wants them.
 *
 * One HeyGen API key serves the whole application, so every kit and glossary
 * lives in a single shared workspace. Rows this app created carry the user that
 * created them and only that user may rename or delete them; rows that merely
 * arrived through a sync belong to the workspace and stay read-only, so nobody
 * can pull a kit out from under everyone else.
 */
class BrandController extends Controller
{
    private const SYNCED_CACHE_KEY = 'video_agent_brand_synced';

    public function __construct(private readonly HeyGenClient $client) {}

    public function index(): JsonResponse
    {
        if (Helper::appIsNotDemo() && ! Cache::has(self::SYNCED_CACHE_KEY)) {
            $this->sync();
        }

        return $this->payload();
    }

    public function refresh(Request $request): RedirectResponse|JsonResponse
    {
        Cache::forget(self::SYNCED_CACHE_KEY);

        $this->sync();

        if ($request->wantsJson()) {
            return $this->payload();
        }

        return back()->with(['type' => 'success', 'message' => __('Brand assets refreshed.')]);
    }

    /**
     * HeyGen builds the kit itself: hand it a public website URL and it
     * extracts the colors, logos and fonts. There is no endpoint that accepts
     * those values directly.
     */
    public function storeKit(Request $request): RedirectResponse|JsonResponse
    {
        if (Helper::appIsDemo()) {
            return $this->refuse($request, __('This feature is disabled in demo mode.'));
        }

        $data = $request->validate([
            'url'  => ['required', 'url', 'max:2048'],
            'name' => ['nullable', 'string', 'max:256'],
        ]);

        $response = $this->client->createBrandKit(
            array_filter($data, static fn ($value) => $value !== null && $value !== ''),
        );

        if (isset($response['error'])) {
            return $this->refuse($request, $response['message'] ?? __('HeyGen rejected the brand kit.'));
        }

        $payload = $response['data'] ?? $response;
        $id = $payload['brand_kit_id'] ?? $payload['id'] ?? null;

        if (blank($id)) {
            return $this->refuse($request, __('HeyGen did not return a brand kit.'));
        }

        VideoAgentBrandKit::query()->updateOrCreate(
            ['heygen_id' => (string) $id],
            [
                'user_id'   => $request->user()->id,
                'type'      => VideoAgentBrandKit::TYPE_KIT,
                'name'      => $payload['name'] ?? $data['name'] ?? $data['url'],
                'payload'   => $payload,
                'synced_at' => now(),
            ],
        );

        if ($request->wantsJson()) {
            return $this->payload();
        }

        return back()->with(['type' => 'success', 'message' => __('Brand kit created.')]);
    }

    /**
     * Rename only. HeyGen also reassigns colour/logo/font roles through this
     * endpoint, but it replaces each role object in full — a role omitted from
     * the payload is cleared for good — so role editing stays out of a form
     * that cannot show what it would erase.
     */
    public function updateKit(Request $request, VideoAgentBrandKit $kit): RedirectResponse|JsonResponse
    {
        if (Helper::appIsDemo()) {
            return $this->refuse($request, __('This feature is disabled in demo mode.'));
        }

        if (! $this->manages($request, $kit)) {
            return $this->refuse($request, __('This brand kit belongs to the workspace and cannot be edited here.'));
        }

        $data = $request->validate(['name' => ['required', 'string', 'max:256']]);

        $response = $this->client->updateBrandKit($kit->heygen_id, ['name' => $data['name']]);

        if (isset($response['error'])) {
            return $this->refuse($request, $response['message'] ?? __('HeyGen rejected the change.'));
        }

        $kit->forceFill(['name' => $data['name'], 'synced_at' => now()])->save();

        if ($request->wantsJson()) {
            return $this->payload();
        }

        return back()->with(['type' => 'success', 'message' => __('Brand kit renamed.')]);
    }

    /**
     * Permanent at HeyGen's end too — the API has no restore.
     */
    public function destroyKit(Request $request, VideoAgentBrandKit $kit): RedirectResponse|JsonResponse
    {
        if (Helper::appIsDemo()) {
            return $this->refuse($request, __('This feature is disabled in demo mode.'));
        }

        if (! $this->manages($request, $kit)) {
            return $this->refuse($request, __('This brand kit belongs to the workspace and cannot be deleted here.'));
        }

        $response = $this->client->deleteBrandKit($kit->heygen_id);

        // A kit already gone upstream should still leave our table, otherwise
        // the row is undeletable forever.
        if (isset($response['error']) && ($response['status'] ?? null) !== 404) {
            return $this->refuse($request, $response['message'] ?? __('HeyGen refused to delete the brand kit.'));
        }

        $kit->delete();

        if ($request->wantsJson()) {
            return $this->payload();
        }

        return back()->with(['type' => 'success', 'message' => __('Brand kit deleted.')]);
    }

    public function storeGlossary(Request $request): RedirectResponse|JsonResponse
    {
        if (Helper::appIsDemo()) {
            return $this->refuse($request, __('This feature is disabled in demo mode.'));
        }

        // HeyGen calls the replacement a `pronunciation`: it is respelling fed
        // to the synthesizer, and it changes the audio only, never captions.
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:64'],
            'terms'                 => ['required', 'array', 'min:1'],
            'terms.*.term'          => ['required', 'string', 'max:191'],
            'terms.*.pronunciation' => ['required', 'string', 'max:191'],
        ]);

        $response = $this->client->createBrandGlossary($data);

        if (isset($response['error'])) {
            return $this->refuse($request, $response['message'] ?? __('HeyGen rejected the glossary.'));
        }

        $payload = $response['data'] ?? $response;

        // The create response echoes the id but not always the terms; keep the
        // ones we just sent so the row is complete without a second fetch.
        $payload['terms'] ??= $data['terms'];

        VideoAgentBrandKit::query()->updateOrCreate(
            ['heygen_id' => (string) ($payload['glossary_id'] ?? $payload['id'])],
            [
                'user_id'   => $request->user()->id,
                'type'      => VideoAgentBrandKit::TYPE_GLOSSARY,
                'name'      => $data['name'],
                'payload'   => $payload,
                'synced_at' => now(),
            ],
        );

        if ($request->wantsJson()) {
            return $this->payload();
        }

        return back()->with(['type' => 'success', 'message' => __('Glossary created.')]);
    }

    /**
     * Both lists in one response so the modal never needs a second round trip.
     */
    private function payload(): JsonResponse
    {
        return response()->json([
            'kits' => VideoAgentBrandKit::query()->kits()->orderBy('name')->get()
                ->map(fn (VideoAgentBrandKit $kit): array => $this->kitRow($kit))->all(),
            'glossaries' => VideoAgentBrandKit::query()->glossaries()->orderBy('name')->get()
                ->map(fn (VideoAgentBrandKit $kit): array => $this->kitRow($kit))->all(),
            'configured' => $this->client->isConfigured(),
        ]);
    }

    /**
     * The list endpoint returns `colors[]` and `logo_url`; the detail endpoint
     * returns `logos[]`, `fonts[]` and role maps instead. Read both so a kit
     * looks the same however it was fetched.
     *
     * @return array<string, mixed>
     */
    private function kitRow(VideoAgentBrandKit $kit): array
    {
        $payload = $kit->payload ?? [];

        return [
            'id'        => $kit->getKey(),
            'heygen_id' => $kit->heygen_id,
            'name'      => $kit->name ?: $kit->heygen_id,
            'colors'    => array_values(array_filter((array) ($payload['colors'] ?? []), 'is_string')),
            'font'      => $payload['fonts'][0]['name'] ?? null,
            'logo'      => $payload['logo_url'] ?? $payload['logos'][0]['url'] ?? null,
            // A freshly built kit reports `loading` until HeyGen finishes
            // crawling the site, so the modal can say so instead of showing
            // an empty swatch row.
            'status'    => $payload['status'] ?? null,
            'terms'     => array_values((array) ($payload['terms'] ?? [])),
            'owned'     => $kit->user_id !== null && $kit->user_id === auth()->id(),
        ];
    }

    private function manages(Request $request, VideoAgentBrandKit $kit): bool
    {
        return $kit->type === VideoAgentBrandKit::TYPE_KIT
            && $kit->user_id !== null
            && $kit->user_id === $request->user()->id;
    }

    private function refuse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->withInput()->with(['type' => 'error', 'message' => $message]);
    }

    private function sync(): void
    {
        if (! $this->client->isConfigured()) {
            return;
        }

        foreach ($this->client->listBrandKits() as $kit) {
            $id = $kit['brand_kit_id'] ?? $kit['id'] ?? null;

            if (blank($id)) {
                continue;
            }

            VideoAgentBrandKit::query()->updateOrCreate(
                ['heygen_id' => (string) $id],
                [
                    'type'      => VideoAgentBrandKit::TYPE_KIT,
                    'name'      => $kit['name'] ?? null,
                    'payload'   => $kit,
                    'synced_at' => now(),
                ],
            );
        }

        foreach ($this->client->listBrandGlossaries() as $glossary) {
            $id = $glossary['glossary_id'] ?? $glossary['id'] ?? null;

            if (blank($id)) {
                continue;
            }

            VideoAgentBrandKit::query()->updateOrCreate(
                ['heygen_id' => (string) $id],
                [
                    'type'      => VideoAgentBrandKit::TYPE_GLOSSARY,
                    'name'      => $glossary['name'] ?? null,
                    'payload'   => $this->glossaryPayload((string) $id, $glossary),
                    'synced_at' => now(),
                ],
            );
        }

        Cache::put(self::SYNCED_CACHE_KEY, now()->timestamp, now()->addMinutes(30));
    }

    /**
     * `GET /v3/brand-glossaries` returns names and timestamps only, so the term
     * list has to be fetched one glossary at a time or the modal reports every
     * glossary as having zero terms.
     *
     * @param  array<string, mixed>  $glossary
     *
     * @return array<string, mixed>
     */
    private function glossaryPayload(string $id, array $glossary): array
    {
        if (isset($glossary['terms'])) {
            return $glossary;
        }

        $response = $this->client->getBrandGlossary($id);

        if (isset($response['error'])) {
            return $glossary;
        }

        return array_merge($glossary, (array) ($response['data'] ?? $response));
    }
}
