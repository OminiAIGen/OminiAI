<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class VideoAgentVoice extends Model
{
    private const LANGUAGES_CACHE_KEY = 'video_agent_voice_languages';

    /**
     * HeyGen is inconsistent about this field: most voices carry an English
     * display name, a handful carry a bare ISO 639-1 code, and a few carry a
     * country or a misspelling. Left alone the filter lists "en" next to
     * "English" as if they were different languages.
     *
     * @var array<string, string>
     */
    private const LANGUAGE_ALIASES = [
        'ar'  => 'Arabic',
        'bg'  => 'Bulgarian',
        'bn'  => 'Bengali',
        'cs'  => 'Czech',
        'da'  => 'Danish',
        'de'  => 'German',
        'el'  => 'Greek',
        'en'  => 'English',
        'es'  => 'Spanish',
        'fa'  => 'Persian',
        'fi'  => 'Finnish',
        'fil' => 'Filipino',
        'fr'  => 'French',
        'he'  => 'Hebrew',
        'hi'  => 'Hindi',
        'hr'  => 'Croatian',
        'hu'  => 'Hungarian',
        'id'  => 'Indonesian',
        'it'  => 'Italian',
        'ja'  => 'Japanese',
        'ko'  => 'Korean',
        'ms'  => 'Malay',
        'nb'  => 'Norwegian',
        'nl'  => 'Dutch',
        'no'  => 'Norwegian',
        'pl'  => 'Polish',
        'pt'  => 'Portuguese',
        'ro'  => 'Romanian',
        'ru'  => 'Russian',
        'sk'  => 'Slovak',
        'sv'  => 'Swedish',
        'ta'  => 'Tamil',
        'th'  => 'Thai',
        'tl'  => 'Filipino',
        'tr'  => 'Turkish',
        'uk'  => 'Ukrainian',
        'ur'  => 'Urdu',
        'vi'  => 'Vietnamese',
        'zh'  => 'Chinese',

        'brazil'    => 'Portuguese',
        'castilian' => 'Spanish',
        'mandarin'  => 'Chinese',
        'portugese' => 'Portuguese',
        'tagalog'   => 'Filipino',
        'turkey'    => 'Turkish',
    ];

    /**
     * Placeholders HeyGen sends when it does not know the language. These are
     * worse than nothing in a filter, so they become null.
     *
     * @var array<int, string>
     */
    private const UNKNOWN_LANGUAGES = ['unknown', 'n/a', 'na', 'none', 'null', '-'];

    protected $table = 'ext_video_agent_voices';

    protected $fillable = [
        'heygen_id',
        'name',
        'language',
        'gender',
        'type',
        'preview_audio_url',
        'support_pause',
        'support_locale',
        'payload',
        'synced_at',
    ];

    protected $casts = [
        'support_pause'  => 'boolean',
        'support_locale' => 'boolean',
        'payload'        => 'array',
        'synced_at'      => 'datetime',
    ];

    public function scopeInLanguage(Builder $query, ?string $language): Builder
    {
        $language = static::normalizeLanguage($language);

        return $language === null ? $query : $query->where('language', $language);
    }

    /**
     * Collapse whatever HeyGen sent into one canonical English display name,
     * so the catalog stores a single value per language and the filter can
     * match on it exactly.
     */
    public static function normalizeLanguage(?string $language): ?string
    {
        $value = trim((string) $language);

        if ($value === '' || in_array(mb_strtolower($value), self::UNKNOWN_LANGUAGES, true)) {
            return null;
        }

        // "en-US", "pt_BR" and "zh-Hans" all name their language before the
        // separator; the region never changes which language it is.
        $base = mb_strtolower((string) preg_split('/[-_]/', $value)[0]);

        return self::LANGUAGE_ALIASES[$base]
            ?? mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    public function scopeOfGender(Builder $query, ?string $gender): Builder
    {
        return blank($gender) ? $query : $query->where('gender', $gender);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return blank($term) ? $query : $query->where('name', 'like', '%' . $term . '%');
    }

    /**
     * Cached because the filter dropdown runs a DISTINCT over the full voice
     * catalog on every page view, and the list only moves when a sync lands.
     *
     * @return array<int, string>
     */
    public static function availableLanguages(): array
    {
        return Cache::remember(
            self::LANGUAGES_CACHE_KEY,
            now()->addMinutes((int) config('video-agent.catalog.filters_ttl', 720)),
            static fn (): array => static::query()
                ->whereNotNull('language')
                ->where('language', '!=', '')
                ->distinct()
                ->orderBy('language')
                ->pluck('language')
                ->all(),
        );
    }

    public static function forgetFilterCache(): void
    {
        Cache::forget(self::LANGUAGES_CACHE_KEY);
    }
}
