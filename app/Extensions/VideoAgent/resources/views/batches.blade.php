@extends('panel.layout.app')
@section('title', __('Batches'))
@section('titlebar_subtitle', __('Render many videos from one avatar and voice in a single job.'))
@section('titlebar_actions', '')

@section('content')
    <div class="flex flex-col gap-6 py-10">

        @include('video-agent::partials.tool-header')

        <x-card size="lg">
            <form
                class="flex flex-col gap-5"
                method="post"
                action="{{ route('dashboard.user.video-agent.batches.store') }}"
            >
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <x-forms.input
                        id="batch_avatar_id"
                        name="avatar_id"
                        type="select"
                        label="{{ __('Avatar') }}"
                    >
                        <option value="">{{ __('Select an avatar') }}</option>
                        @foreach ($avatars as $avatar)
                            <option value="{{ $avatar->heygen_id }}">{{ $avatar->name ?: $avatar->heygen_id }}</option>
                        @endforeach
                    </x-forms.input>

                    <x-forms.input
                        id="batch_voice_id"
                        name="voice_id"
                        type="select"
                        label="{{ __('Voice') }}"
                    >
                        <option value="">{{ __('Select a voice') }}</option>
                        @foreach ($voices as $voice)
                            <option value="{{ $voice->heygen_id }}">{{ $voice->name }} · {{ $voice->language }}</option>
                        @endforeach
                    </x-forms.input>
                </div>

                <x-forms.input
                    id="scripts"
                    name="scripts"
                    type="textarea"
                    rows="12"
                    label="{{ __('Scripts') }}"
                    tooltip="{{ __('One script per block, separated by a blank line. Up to 100 per batch.') }}"
                    placeholder="{{ __("Hi Ana, here is your update.\n\nHi Ben, here is your update.") }}"
                />

                <x-button type="submit">{{ __('Submit batch') }}</x-button>
            </form>
        </x-card>

        @if ($batches->isNotEmpty())
            <x-card size="lg">
                <x-slot:head>
                    <h3 class="mb-0 text-base">{{ __('Recent batches') }}</h3>
                </x-slot:head>

                @foreach ($batches as $batch)
                    <div class="mb-3 rounded-card border border-card-border p-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="grow">
                                <div class="text-sm font-medium">{{ $batch->item_count }} {{ __('videos') }}</div>
                                <div class="text-2xs text-label">{{ $batch->created_at?->diffForHumans() }}</div>
                            </div>

                            <x-badge>{{ Str::headline((string) $batch->status) }}</x-badge>

                            <form method="post" action="{{ route('dashboard.user.video-agent.batches.refresh', $batch) }}">
                                @csrf
                                <x-button type="submit" variant="ghost" size="sm">
                                    <x-tabler-refresh class="size-4" />
                                </x-button>
                            </form>
                        </div>

                        <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-foreground/10">
                            <div
                                class="h-full rounded-full bg-primary transition-all"
                                style="width: {{ $batch->progressPercent() }}%"
                            ></div>
                        </div>

                        @if ($batch->error_message)
                            <p class="mt-2 text-2xs text-red-500">{{ $batch->error_message }}</p>
                        @endif
                    </div>
                @endforeach
            </x-card>
        @endif
    </div>
@endsection
