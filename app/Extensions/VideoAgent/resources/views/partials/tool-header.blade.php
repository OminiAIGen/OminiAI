{{--
    Header for the standalone tool pages. Replaces the old 12-tab bar with a
    back link to the workspace plus the handful of sibling tools.
--}}
@php
    $videoAgentTools = array_filter([
        'dashboard.user.video-agent.create' => __('Create manually'),
        'dashboard.user.video-agent.speech.index' => __('Text to Speech'),
        'dashboard.user.video-agent.tools.index' => __('Post-production'),
        'dashboard.user.video-agent.batches.index' => __('Batches'),
        'dashboard.user.video-agent.hyperframes.index' => __('HyperFrames'),
    ], fn ($label, $route) => Route::has($route), ARRAY_FILTER_USE_BOTH);
@endphp

<div class="flex flex-wrap items-center gap-x-6 gap-y-3">
    <a
        class="inline-flex shrink-0 items-center gap-1.5 text-2xs font-medium text-label transition hover:text-foreground"
        href="{{ route('dashboard.user.video-agent.index') }}"
    >
        <x-tabler-arrow-left class="size-4 rtl:rotate-180" />
        {{ __('Video Agent') }}
    </a>

    <nav class="flex flex-wrap items-center gap-1">
        @foreach ($videoAgentTools as $route => $label)
            <a
                class="rounded-button px-3 py-1.5 text-2xs text-label transition hover:bg-foreground/5 hover:text-foreground [&.active]:bg-foreground/5 [&.active]:text-foreground"
                href="{{ route($route) }}"
                @class(['active' => request()->routeIs($route)])
            >{{ $label }}</a>
        @endforeach
    </nav>

    @isset($usage)
        <div class="ms-auto max-sm:hidden">
            @include('video-agent::partials.usage-meter', ['usage' => $usage])
        </div>
    @endisset
</div>
