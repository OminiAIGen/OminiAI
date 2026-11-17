@extends('panel.layout.app')
@section('title', __('Text to Speech'))
@section('titlebar_subtitle', __('Generate a standalone voiceover with any AI voice.'))
@section('titlebar_actions', '')

@section('content')
    <div class="flex flex-col gap-6 py-10">

        @include('video-agent::partials.tool-header')

        <x-card size="lg">
            <form
                method="post"
                action="{{ route('dashboard.user.video-agent.speech.store') }}"
            >
                @csrf

                <x-forms.input
                    id="text"
                    name="text"
                    type="textarea"
                    rows="5"
                    label="{{ __('Text') }}"
                    placeholder="{{ __('What should the voice say?') }}"
                />

                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <x-forms.input
                        id="voice_id"
                        name="voice_id"
                        type="select"
                        label="{{ __('Voice') }}"
                    >
                        <option value="">{{ __('Select a voice') }}</option>
                        @foreach ($voices as $voice)
                            <option value="{{ $voice->heygen_id }}">{{ $voice->name }} · {{ $voice->language }}</option>
                        @endforeach
                    </x-forms.input>

                    <x-forms.input
                        id="speed"
                        name="speed"
                        type="number"
                        step="0.1"
                        min="0.5"
                        max="1.5"
                        label="{{ __('Speed') }}"
                        value="1"
                    />
                </div>

                <x-button class="mt-6 w-full" type="submit">
                    {{ __('Generate audio') }}
                </x-button>
            </form>
        </x-card>

        @if ($clips->isNotEmpty())
            <x-card size="lg">
                <x-slot:head>
                    <h3 class="mb-0 text-base">{{ __('Recent clips') }}</h3>
                </x-slot:head>

                @foreach ($clips as $clip)
                    <div class="mb-3 flex flex-wrap items-center gap-4 rounded-card border border-card-border p-3">
                        <div class="grow">
                            <div class="text-sm">{{ $clip->displayTitle() }}</div>
                            <div class="text-2xs text-label">{{ $clip->status->label() }}</div>
                        </div>

                        @if ($clip->playbackUrl())
                            <audio class="h-8" src="{{ $clip->playbackUrl() }}" controls preload="none"></audio>
                        @endif
                    </div>
                @endforeach
            </x-card>
        @endif
    </div>
@endsection
