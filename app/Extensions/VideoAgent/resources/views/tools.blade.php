@php
    $enabled = static function (string $feature) use ($features): bool {
        $stored = json_decode((string) setting('video_agent_enabled_features', 'null'), true);

        return is_array($stored)
            ? in_array($feature, $stored, true)
            : (bool) ($features[$feature] ?? false);
    };
@endphp

@extends('panel.layout.app')
@section('title', __('Post-production'))
@section('titlebar_subtitle', __('Lipsync, clip, clean up and translate videos you already have.'))
@section('titlebar_actions', '')

@section('content')
    <div class="flex flex-col gap-6 py-10">

        @include('video-agent::partials.tool-header')

        <div class="grid gap-6 lg:grid-cols-2">
            @if ($enabled('lipsync'))
                <x-card size="lg">
                    <x-slot:head>
                        <h3 class="mb-0 text-base">{{ __('Lipsync') }}</h3>
                    </x-slot:head>

                    <p class="mb-4 text-2xs text-label">
                        {{ __('Replace or dub the audio on an existing video. Precision mode costs more per second than speed.') }}
                    </p>

                    <form
                        class="flex flex-col gap-4"
                        method="post"
                        action="{{ route('dashboard.user.video-agent.tools.lipsync') }}"
                    >
                        @csrf
                        <x-forms.input id="lipsync_video_url" name="video_url" label="{{ __('Video URL') }}" placeholder="https://…" />
                        <x-forms.input id="lipsync_audio_url" name="audio_url" label="{{ __('Audio URL') }}" placeholder="https://…" />

                        <x-forms.input
                            id="lipsync_mode"
                            name="mode"
                            type="select"
                            label="{{ __('Mode') }}"
                        >
                            <option value="speed">{{ __('Speed') }}</option>
                            <option value="precision">{{ __('Precision') }}</option>
                        </x-forms.input>

                        <x-button type="submit" variant="outline">{{ __('Run lipsync') }}</x-button>
                    </form>
                </x-card>
            @endif

            @if ($enabled('clipping'))
                <x-card size="lg">
                    <x-slot:head>
                        <h3 class="mb-0 text-base">{{ __('AI clipping') }}</h3>
                    </x-slot:head>

                    <p class="mb-4 text-2xs text-label">
                        {{ __('Turn a long recording into short, captioned clips.') }}
                    </p>

                    <form
                        class="flex flex-col gap-4"
                        method="post"
                        action="{{ route('dashboard.user.video-agent.tools.clip') }}"
                    >
                        @csrf
                        <x-forms.input id="clip_video_url" name="video_url" label="{{ __('Video URL') }}" placeholder="https://…" />
                        <x-forms.input id="clip_count" name="clip_count" type="number" min="1" max="20" label="{{ __('How many clips') }}" value="3" />

                        <x-button type="submit" variant="outline">{{ __('Create clips') }}</x-button>
                    </form>
                </x-card>
            @endif

            @if ($enabled('filler'))
                <x-card size="lg">
                    <x-slot:head>
                        <h3 class="mb-0 text-base">{{ __('Filler word removal') }}</h3>
                    </x-slot:head>

                    <p class="mb-4 text-2xs text-label">
                        {{ __('Strip “um”, “uh” and dead air. Billed with a one minute minimum.') }}
                    </p>

                    <form
                        class="flex flex-col gap-4"
                        method="post"
                        action="{{ route('dashboard.user.video-agent.tools.filler') }}"
                    >
                        @csrf
                        <x-forms.input id="filler_video_url" name="video_url" label="{{ __('Video URL') }}" placeholder="https://…" />

                        <x-button type="submit" variant="outline">{{ __('Clean up audio') }}</x-button>
                    </form>
                </x-card>
            @endif

            @if ($enabled('proofread'))
                <x-card size="lg">
                    <x-slot:head>
                        <h3 class="mb-0 text-base">{{ __('Proofread') }}</h3>
                    </x-slot:head>

                    <p class="mb-4 text-2xs text-label">
                        {{ __('Extract the subtitles, correct them, then regenerate the video from your edits.') }}
                    </p>

                    <form
                        class="flex flex-col gap-4"
                        method="post"
                        action="{{ route('dashboard.user.video-agent.proofread.store') }}"
                    >
                        @csrf
                        <x-forms.input id="proofread_video_url" name="video_url" label="{{ __('Video URL') }}" placeholder="https://…" />

                        {{--
                            HeyGen files proofreading under video translation and
                            requires a target language even when it matches the
                            source, so this is not optional.
                        --}}
                        <div>
                            <label class="form-label" for="proofread_output_language">{{ __('Subtitle language') }}</label>
                            <select
                                class="form-select"
                                id="proofread_output_language"
                                name="output_language"
                            >
                                @foreach ($languages as $language)
                                    @php
                                        $value = is_array($language) ? ($language['name'] ?? $language['language'] ?? null) : $language;
                                    @endphp
                                    @continue(blank($value))
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-button type="submit" variant="outline">{{ __('Start proofread') }}</x-button>
                    </form>
                </x-card>
            @endif

            @if ($enabled('translation'))
                <x-card size="lg">
                    <x-slot:head>
                        <h3 class="mb-0 text-base">{{ __('Translation') }}</h3>
                    </x-slot:head>

                    <x-alert class="mb-4" variant="lg">
                        <p>{{ __('The Video Dubbing extension also covers this workflow. Enable only one to avoid double billing.') }}</p>
                    </x-alert>

                    <form
                        class="flex flex-col gap-4"
                        method="post"
                        action="{{ route('dashboard.user.video-agent.tools.translate') }}"
                    >
                        @csrf
                        <x-forms.input id="translate_video_url" name="video_url" label="{{ __('Video URL') }}" placeholder="https://…" />

                        <div>
                            <label class="form-label" for="output_languages">{{ __('Target languages') }}</label>
                            <select
                                class="form-select"
                                id="output_languages"
                                name="output_languages[]"
                                multiple
                                size="6"
                            >
                                @foreach ($languages as $language)
                                    @php
                                        $value = is_array($language) ? ($language['name'] ?? $language['language'] ?? null) : $language;
                                    @endphp
                                    @continue(blank($value))
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-button type="submit" variant="outline">{{ __('Translate') }}</x-button>
                    </form>
                </x-card>
            @endif
        </div>

        @if ($jobs->isNotEmpty())
            <x-card size="lg">
                <x-slot:head>
                    <h3 class="mb-0 text-base">{{ __('Recent jobs') }}</h3>
                </x-slot:head>

                @foreach ($jobs as $job)
                    <div class="mb-3 flex flex-wrap items-center gap-4 rounded-card border border-card-border p-3">
                        <div class="grow">
                            <div class="text-sm">{{ $job->displayTitle() }}</div>
                            <div class="text-2xs text-label">{{ $job->kind->label() }}</div>
                        </div>

                        <x-badge>{{ $job->status->label() }}</x-badge>

                        @if ($job->playbackUrl())
                            <x-button href="{{ $job->playbackUrl() }}" size="sm" variant="ghost" target="_blank">
                                {{ __('Open') }}
                            </x-button>
                        @endif
                    </div>
                @endforeach
            </x-card>
        @endif
    </div>
@endsection
