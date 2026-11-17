<?php

declare(strict_types=1);

use App\Extensions\VideoAgent\System\Models\VideoAgentVoice;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Catalogs synced before the normalizer landed hold "en" alongside
     * "English", "Turkey" alongside "Turkish", and "unknown" as if it were a
     * language. Collapse them so the picker filter lists each language once.
     */
    public function up(): void
    {
        VideoAgentVoice::query()
            ->distinct()
            ->pluck('language')
            ->each(static function (?string $raw): void {
                $normalized = VideoAgentVoice::normalizeLanguage($raw);

                if ($normalized === $raw) {
                    return;
                }

                VideoAgentVoice::query()
                    ->when(
                        $raw === null,
                        static fn ($query) => $query->whereNull('language'),
                        static fn ($query) => $query->where('language', $raw),
                    )
                    ->toBase()
                    ->update(['language' => $normalized]);
            });

        VideoAgentVoice::forgetFilterCache();
    }

    /**
     * The original values are not recoverable, and the next catalog sync
     * rewrites the column anyway.
     */
    public function down(): void {}
};
