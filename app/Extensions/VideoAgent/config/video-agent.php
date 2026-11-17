<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | HeyGen v3 API
    |--------------------------------------------------------------------------
    |
    | The API key itself is NOT configured here. It is the core-owned
    | `heygen_secret_key` column on the `settings` table, shared with the
    | AiPersona and VideoDubbing extensions.
    |
    */

    'api' => [
        'base_url' => env('VIDEO_AGENT_BASE_URL', 'https://api.heygen.com'),
        'timeout'  => 120,
        'retries'  => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature flags
    |--------------------------------------------------------------------------
    |
    | Translation is disabled by default because the VideoDubbing extension
    | already owns HeyGen video translation.
    |
    */

    'features' => [
        'video_agent' => true,
        'styles'      => true,
        'videos'      => true,
        'avatars'     => true,
        'voices'      => true,
        'assets'      => true,
        'templates'   => true,
        'brand'       => true,
        'lipsync'     => true,
        'clipping'    => true,
        'filler'      => true,
        'proofread'   => true,
        'hyperframes' => true,
        'batches'     => true,
        'translation' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Status polling
    |--------------------------------------------------------------------------
    |
    | The default queue connection is `sync`, so the polling job must never
    | assume a worker is running. `video-agent:poll` sweeps stale rows.
    |
    */

    'poll' => [
        'interval_seconds' => 15,
        'max_attempts'     => 240,
        'stale_after'      => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Catalog cache
    |--------------------------------------------------------------------------
    |
    | Look Packs, avatar looks and voices are thousands of rows behind a cursor
    | API. They are mirrored locally by SyncHeyGenCatalogJob and only refetched
    | once `ttl_minutes` has passed, so a page view never waits on HeyGen.
    |
    */

    'catalog' => [
        'ttl_minutes'  => 720,
        'page_size'    => 100,
        'max_pages'    => 20,
        'running_ttl'  => 900,
        'filters_ttl'  => 720,
    ],

    /*
    |--------------------------------------------------------------------------
    | Generation defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'mode'         => 'generate',
        'orientation'  => '',
        'resolution'   => '1080p',
        'engine'       => 'avatar_iv',
        'aspect_ratio' => '16:9',
    ],

    /*
    |--------------------------------------------------------------------------
    | Target lengths offered in the composer
    |--------------------------------------------------------------------------
    |
    | HeyGen has no duration parameter — its guide says to put the length in
    | the prompt — so these seconds are appended as a sentence. An empty
    | selection means "let the agent decide".
    |
    */

    'durations' => [15, 30, 60],

    /*
    |--------------------------------------------------------------------------
    | Limits enforced before the request reaches HeyGen
    |--------------------------------------------------------------------------
    */

    'limits' => [
        'prompt_min'      => 1,
        'prompt_max'      => 10000,
        'max_files'       => 20,
        'asset_max_bytes' => 32 * 1024 * 1024,
        'asset_mimes'     => [
            'image/png', 'image/jpeg', 'video/mp4', 'video/webm',
            'audio/mpeg', 'audio/wav', 'audio/x-wav', 'application/pdf',
            'application/x-subrip', 'text/plain',
        ],
    ],
];
