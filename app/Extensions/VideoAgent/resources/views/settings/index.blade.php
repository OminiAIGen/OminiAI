@extends('panel.layout.settings', ['disable_tblr' => true, 'layout' => 'wide'])
@section('title', __('Video Agent Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('Configure the HeyGen Video Agent defaults, features and webhook.'))

@php
    $enabledFeatures = json_decode((string) setting('video_agent_enabled_features', '[]'), true) ?: array_keys(array_filter($features));
    $accountData = $account['data'] ?? [];
    $accountFailed = isset($account['error']);
@endphp

@section('settings')
    <div class="flex flex-col gap-8">

        {{-- API key + wallet --}}
        <x-card size="lg">
            <x-slot:head>
                <h3 class="mb-0 text-base">
                    {{ __('HeyGen Account') }}
                </h3>
            </x-slot:head>

            @if ($accountFailed)
                <x-alert variant="lg" type="danger">
                    <p class="mb-2">
                        @if ($account['error'] === 'not_configured')
                            {{ __('No HeyGen API key is set yet.') }}
                        @else
                            {{ $account['message'] ?? __('Could not reach HeyGen.') }}
                        @endif
                    </p>
                    <p class="text-xs">
                        @if (Route::has('dashboard.admin.settings.heygen'))
                            {{ __('Video Agent uses the same API key as the other HeyGen features. Set it in') }}
                            <a
                                class="underline"
                                href="{{ route('dashboard.admin.settings.heygen') }}"
                            >{{ __('HeyGen Settings') }}</a>.
                        @else
                            {{ __('Set the HeyGen API key in the settings table as heygen_secret_key.') }}
                        @endif
                    </p>
                </x-alert>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($accountData as $key => $value)
                        @continue(is_array($value))
                        <div class="rounded-card border border-card-border bg-surface p-4">
                            <div class="text-2xs text-label">
                                {{ Str::headline((string) $key) }}
                            </div>
                            <div class="mt-1 break-all text-sm font-medium">
                                {{ $value === null || $value === '' ? '—' : $value }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <x-button
                        href="{{ route('dashboard.admin.video-agent.settings', ['refresh' => 1]) }}"
                        variant="outline"
                        size="sm"
                    >
                        <x-tabler-refresh class="size-4" />
                        {{ __('Refresh balance') }}
                    </x-button>
                    <span class="text-2xs text-label">
                        {{ __('Cached for 5 minutes.') }}
                    </span>
                </div>
            @endif
        </x-card>

        {{-- Webhook --}}
        <x-card size="lg">
            <x-slot:head>
                <h3 class="mb-0 text-base">
                    {{ __('Webhook') }}
                </h3>
            </x-slot:head>

            <p class="mb-4 text-2xs text-label">
                {{ __('HeyGen pushes render events here. Deliveries are verified with an HMAC-SHA256 signature and de-duplicated by event id. Polling still runs as a fallback.') }}
            </p>

            <div class="mb-4 break-all rounded-input border border-input-border bg-input-background px-4 py-3 text-2xs">
                {{ $webhookUrl }}
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <form method="post" action="{{ route('dashboard.admin.video-agent.webhook.register') }}">
                    @csrf
                    <x-button type="submit" size="sm">
                        {{ $hasWebhookSecret ? __('Re-register endpoint') : __('Register endpoint') }}
                    </x-button>
                </form>

                @if ($hasWebhookSecret)
                    <form method="post" action="{{ route('dashboard.admin.video-agent.webhook.delete') }}">
                        @csrf
                        <x-button type="submit" variant="outline" size="sm">
                            {{ __('Remove endpoint') }}
                        </x-button>
                    </form>

                    <span class="text-2xs text-label">
                        {{ __('Endpoint') }}: {{ $webhookEndpoint ?: '—' }}
                    </span>
                @endif
            </div>
        </x-card>

        <form
            class="flex flex-col gap-8"
            method="post"
            action="{{ route('dashboard.admin.video-agent.update') }}"
        >
            @csrf

            {{-- Generation defaults --}}
            <section>
                <h3 class="mb-5">
                    {{ __('Generation Defaults') }}
                </h3>

                <div class="flex flex-col gap-5">
                    <x-forms.input
                        id="video_agent_default_mode"
                        name="video_agent_default_mode"
                        type="select"
                        size="lg"
                        label="{{ __('Default Mode') }}"
                        tooltip="{{ __('Review storyboard shows a scene-by-scene plan before HeyGen renders anything. One-shot renders straight away.') }}"
                    >
                        @foreach (\App\Extensions\VideoAgent\System\Enums\SessionModeEnum::cases() as $mode)
                            <option
                                value="{{ $mode->value }}"
                                @selected(setting('video_agent_default_mode', config('video-agent.defaults.mode')) === $mode->value)
                            >{{ $mode->label() }}</option>
                        @endforeach
                    </x-forms.input>

                    <x-forms.input
                        id="video_agent_default_orientation"
                        name="video_agent_default_orientation"
                        type="select"
                        size="lg"
                        label="{{ __('Default Orientation') }}"
                    >
                        {{-- HeyGen accepts landscape or portrait only; Auto omits the field so it detects from the content. --}}
                        @foreach (['' => __('Auto'), 'portrait' => __('Portrait'), 'landscape' => __('Landscape')] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(setting('video_agent_default_orientation', config('video-agent.defaults.orientation')) === $value)
                            >{{ $label }}</option>
                        @endforeach
                    </x-forms.input>

                    <x-forms.input
                        id="video_agent_default_resolution"
                        name="video_agent_default_resolution"
                        type="select"
                        size="lg"
                        label="{{ __('Default Resolution') }}"
                        tooltip="{{ __('HeyGen bills per second of output. 4K costs the same per second but takes longer to render.') }}"
                    >
                        @foreach (['720p', '1080p', '4k'] as $value)
                            <option
                                value="{{ $value }}"
                                @selected(setting('video_agent_default_resolution', config('video-agent.defaults.resolution')) === $value)
                            >{{ strtoupper($value) }}</option>
                        @endforeach
                    </x-forms.input>

                    <x-forms.input
                        id="video_agent_poll_interval"
                        name="video_agent_poll_interval"
                        type="number"
                        size="lg"
                        min="5"
                        max="300"
                        label="{{ __('Status Poll Interval (seconds)') }}"
                        tooltip="{{ __('Used as the fallback when a webhook delivery is missed. Video Agent renders take roughly 5-10x the final video length.') }}"
                        :value="setting('video_agent_poll_interval', config('video-agent.poll.interval_seconds'))"
                    />
                </div>
            </section>

            <hr>

            {{-- Feature toggles --}}
            <section>
                <h3 class="mb-1">
                    {{ __('Modules') }}
                </h3>
                <p class="mb-5 text-2xs text-label">
                    {{ __('Turn off any HeyGen module you do not want exposed to users. Video translation is off by default because the Video Dubbing extension already covers it.') }}
                </p>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($features as $feature => $default)
                        <x-forms.input
                            id="video_agent_feature_{{ $feature }}"
                            name="video_agent_enabled_features[]"
                            type="checkbox"
                            switcher
                            value="{{ $feature }}"
                            label="{{ Str::headline($feature) }}"
                            :checked="in_array($feature, $enabledFeatures, true)"
                        />
                    @endforeach
                </div>
            </section>

            <x-button
                type="submit"
                class="w-full"
            >
                {{ __('Save') }}
            </x-button>
        </form>
    </div>
@endsection
