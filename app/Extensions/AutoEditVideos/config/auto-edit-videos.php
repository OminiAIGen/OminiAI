<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Rendley API
    |--------------------------------------------------------------------------
    |
    | The API key itself is NOT configured here. It lives in the key/value
    | settings store under `auto_edit_videos_api_key` so an administrator can
    | rotate it from the panel without a deploy.
    |
    | `agent_url` is absolute rather than a path because the auto-edit agent is
    | NOT served from `base_url`: it lives on `mcp.rendley.com`, while exports,
    | jobs and account calls live on `api.rendley.com`. Posting the agent call to
    | the api host clears its auth layer and then falls through to a plain-text
    | 404, which reads as a missing project rather than a missing route.
    |
    */

    'api' => [
        'base_url'  => env('AUTO_EDIT_VIDEOS_BASE_URL', 'https://api.rendley.com'),
        'agent_url' => env('AUTO_EDIT_VIDEOS_AGENT_URL', 'https://mcp.rendley.com/v1/agent'),
        'timeout'   => 120,
        'retries'   => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Status polling
    |--------------------------------------------------------------------------
    |
    | Rendley has no webhooks, so every job is polled. The default queue
    | connection is `sync`, so the polling job must never assume a worker is
    | running — `auto-edit-videos:poll` sweeps rows nothing else advanced.
    |
    | `max_attempts` is a hard ceiling: without it a job that never reaches a
    | terminal state re-queues itself forever.
    |
    */

    'poll' => [
        'interval_seconds' => 5,
        'backoff_after'    => 24,
        'backoff_seconds'  => 10,
        'max_attempts'     => 240,
        'stale_after'      => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Export defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'target_resolution' => '1080p',
        'codec'             => 'h264',
        'quality'           => 'high',
        'aspect_ratio'      => '16:9',
    ],

    'resolutions'   => ['720p', '1080p', '4K'],
    'aspect_ratios' => ['16:9', '9:16', '1:1', '4:5'],

    /*
    |--------------------------------------------------------------------------
    | Target lengths offered in the composer
    |--------------------------------------------------------------------------
    |
    | Rendley takes no duration parameter, so a chosen length is appended to the
    | prompt as a sentence. An empty selection means "let the agent decide".
    |
    */

    'durations' => [15, 30, 60, 90, 180],

    /*
    |--------------------------------------------------------------------------
    | Limits enforced before the request reaches Rendley
    |--------------------------------------------------------------------------
    */

    'limits' => [
        'prompt_min'       => 1,
        'prompt_max'       => 5000,
        'max_sources'      => 10,
        'source_max_bytes' => 512 * 1024 * 1024,
        'source_mimes'     => [
            'video/mp4', 'video/quicktime', 'video/webm', 'video/x-m4v',
            'audio/mpeg', 'audio/wav', 'audio/x-wav',
            'image/png', 'image/jpeg', 'image/webp',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Hosts Rendley can never reach
    |--------------------------------------------------------------------------
    |
    | Rendley fetches every source over the public internet. A local dev host
    | fails there with MEDIA_MISSING_PERMANENT_URL, which reads as a vendor
    | outage unless we reject it up front with our own message.
    |
    */

    'blocked_host_suffixes' => ['.test', '.local', '.localhost', '.internal', '.invalid'],
    'blocked_hosts'         => ['localhost', '127.0.0.1', '::1', '0.0.0.0'],

    'storage' => [
        'disk'      => 'uploads',
        'directory' => 'media/videos',
    ],
];
