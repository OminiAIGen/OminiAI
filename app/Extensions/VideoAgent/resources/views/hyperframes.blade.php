@extends('panel.layout.app')
@section('title', __('HyperFrames'))
@section('titlebar_subtitle', __('Render a HyperFrames composition — the same HTML-to-video engine the agent composes with.'))
@section('titlebar_actions', '')

@section('content')
    <div class="flex flex-col gap-6 py-10">

        @include('video-agent::partials.tool-header')

        <x-card size="lg">
            <form
                class="flex flex-col gap-5"
                method="post"
                action="{{ route('dashboard.user.video-agent.hyperframes.store') }}"
            >
                @csrf

                <x-forms.input
                    id="composition_url"
                    name="composition_url"
                    label="{{ __('Composition zip URL') }}"
                    placeholder="https://…/composition.zip"
                />

                <x-forms.input
                    id="hyperframes_title"
                    name="title"
                    label="{{ __('Title') }}"
                />

                <div class="grid gap-5 md:grid-cols-2">
                    <x-forms.input
                        id="fps"
                        name="fps"
                        type="number"
                        min="24"
                        max="60"
                        label="{{ __('Frames per second') }}"
                        value="30"
                    />

                    <x-forms.input
                        id="hyperframes_resolution"
                        name="resolution"
                        type="select"
                        label="{{ __('Resolution') }}"
                    >
                        @foreach (['720p', '1080p', '4k'] as $value)
                            <option value="{{ $value }}">{{ strtoupper($value) }}</option>
                        @endforeach
                    </x-forms.input>
                </div>

                <x-button type="submit">{{ __('Render') }}</x-button>
            </form>
        </x-card>

        @if ($renders->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($renders as $render)
                    @include('video-agent::partials.video-card', ['video' => $render])
                @endforeach
            </div>
        @endif
    </div>
@endsection
