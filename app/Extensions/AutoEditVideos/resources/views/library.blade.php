@extends('panel.layout.app')
@section('title', __('Auto Edit Videos'))
@section('titlebar_subtitle', __('Every video this tool has rendered for you.'))

@section('titlebar_actions')
    <x-button
        size="md"
        href="{{ route('dashboard.user.auto-edit-videos.index') }}"
    >
        <x-tabler-plus class="size-4" />
        {{ __('New edit') }}
    </x-button>
@endsection

@section('content')
    <div class="lqd-auto-edit-library py-7">
        @if ($renders->isEmpty())
            <x-card size="lg">
                <div class="py-12">
                    <x-empty-state
                        icon="tabler-movie"
                        :title="__('Nothing rendered yet')"
                        :description="__('Start an edit and your finished videos will collect here.')"
                    >
                        <x-button
                            class="mt-5"
                            size="md"
                            href="{{ route('dashboard.user.auto-edit-videos.index') }}"
                        >
                            <x-tabler-plus class="size-4" />
                            {{ __('Start an edit') }}
                        </x-button>
                    </x-empty-state>
                </div>
            </x-card>
        @else
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($renders as $render)
                    @include('auto-edit-videos::partials.render-card', ['render' => $render])
                @endforeach
            </div>

            @if ($renders->hasPages())
                <div class="mt-6">
                    {{ $renders->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
