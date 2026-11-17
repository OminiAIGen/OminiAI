<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

/**
 * HeyGen namespaces these under `brand-kits` / `brand-glossaries`, not
 * `brand/kits`. The slash form returns an HTML 404, so every call through it
 * failed with a body the JSON decoder could not explain.
 *
 * The two records cover different halves of "on brand" and no endpoint accepts
 * both: a kit carries the look and only Video Agent takes `brand_kit_id`, while
 * a glossary carries pronunciation and goes to Studio, Templates and
 * translation as `brand_glossary_id`.
 */
trait HasBrand
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function listBrandKits(): array
    {
        return $this->paginate('/v3/brand-kits');
    }

    /**
     * @return array<string, mixed>
     */
    public function getBrandKit(string $kitId): array
    {
        return $this->get("/v3/brand-kits/{$kitId}");
    }

    /**
     * Kits are built, not uploaded: hand HeyGen a public website URL and it
     * crawls the site for colors, logos and fonts. The import is asynchronous,
     * so the kit comes back with `status: loading`.
     *
     * @param  array{url: string, name?: string}  $payload
     *
     * @return array<string, mixed>
     */
    public function createBrandKit(array $payload): array
    {
        return $this->post('/v3/brand-kits', $payload);
    }

    /**
     * Renames a kit or reassigns its color/logo/font roles.
     *
     * Each role object is replaced in full rather than merged, so a role left
     * out of the payload is cleared and never recomputed. Sending `null` is
     * rejected outright — omit a field to leave it alone. Role edits need an
     * assembled kit and answer 409 while the import is still `loading`;
     * renaming works at any time.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function updateBrandKit(string $kitId, array $payload): array
    {
        return $this->patch("/v3/brand-kits/{$kitId}", $payload);
    }

    /**
     * Permanent — the API has no restore. Videos already rendered keep their
     * colors, but a session still pointing at the kit reports an invalid id on
     * its next use.
     *
     * @return array<string, mixed>
     */
    public function deleteBrandKit(string $kitId): array
    {
        return $this->delete("/v3/brand-kits/{$kitId}");
    }

    /**
     * The list endpoint returns names and timestamps only. Terms come from
     * `getBrandGlossary()`, one call per glossary.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listBrandGlossaries(): array
    {
        return $this->paginate('/v3/brand-glossaries');
    }

    /**
     * @return array<string, mixed>
     */
    public function getBrandGlossary(string $glossaryId): array
    {
        return $this->get("/v3/brand-glossaries/{$glossaryId}");
    }

    /**
     * @param  array{name: string, terms?: array<int, array{term: string, pronunciation: string}>}  $payload
     *
     * @return array<string, mixed>
     */
    public function createBrandGlossary(array $payload): array
    {
        return $this->post('/v3/brand-glossaries', $payload);
    }

    /**
     * Replaces the name or the term list in full; an omitted field is left
     * unchanged.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function updateBrandGlossary(string $glossaryId, array $payload): array
    {
        return $this->patch("/v3/brand-glossaries/{$glossaryId}", $payload);
    }
}
