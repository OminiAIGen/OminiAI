@extends('panel.layout.app')
@section('title', __('Proofread'))
@section('titlebar_subtitle', __('Correct the extracted subtitles, then regenerate the video from them.'))
@section('titlebar_actions', '')

@section('content')
    <div class="flex flex-col gap-6 py-10">

        @include('video-agent::partials.tool-header')

        <x-card size="lg">
            <div class="mb-4 text-2xs text-label">{{ $job->displayTitle() }}</div>

            <form
                method="post"
                action="{{ route('dashboard.user.video-agent.proofread.update', $job) }}"
            >
                @csrf

                <x-forms.input
                    id="srt"
                    name="srt"
                    type="textarea"
                    rows="18"
                    label="{{ __('Subtitles (SRT)') }}"
                >{{ old('srt', $srt) }}</x-forms.input>

                <x-button class="mt-5" type="submit" variant="outline">
                    {{ __('Save subtitles') }}
                </x-button>
            </form>

            <form
                class="mt-3"
                method="post"
                action="{{ route('dashboard.user.video-agent.proofread.generate', $job) }}"
            >
                @csrf
                <x-button type="submit">
                    {{ __('Regenerate video') }}
                </x-button>
            </form>
        </x-card>
    </div>
@endsection
