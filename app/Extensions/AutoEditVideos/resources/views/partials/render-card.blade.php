@php
    $isBusy = !$render->isTerminal();
    $aspect = $render->session?->aspect_ratio ?? null;
    $videoClass = match ($aspect) {
        '9:16'  => 'aspect-[9/16] mx-auto max-h-[520px] w-auto',
        '1:1'   => 'aspect-square mx-auto max-h-[520px] w-auto',
        '4:5'   => 'aspect-[4/5] mx-auto max-h-[520px] w-auto',
        default => 'aspect-video w-full',
    };
@endphp

<div class="border-card-border rounded-card border p-3">
    @if ($render->isComplete() && $render->playbackUrl())
        <video
            class="mb-3 {{ $videoClass }} rounded-input bg-black object-contain"
            src="{{ $render->playbackUrl() }}"
            controls
            preload="metadata"
            @if ($render->thumbnail_url) poster="{{ $render->thumbnail_url }}" @endif
        ></video>
    @endif

    <div class="flex items-center justify-between gap-3">
        <div class="min-w-0">
            <p class="truncate text-2xs font-medium">{{ $render->displayTitle() }}</p>
            <p class="text-label mt-0.5 text-4xs">
                {{ optional($render->created_at)->diffForHumans() }}
                @if ($render->duration_seconds)
                    &middot; {{ $render->formattedDuration() }}
                @endif
                @if ($render->target_resolution)
                    &middot; {{ $render->target_resolution }}
                @endif
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            @if ($isBusy)
                <x-tabler-loader-2 class="text-label size-3.5 animate-spin" />
            @endif

            <x-badge variant="{{ $render->status->badgeVariant() }}">
                {{ $render->status->label() }}
            </x-badge>
        </div>
    </div>

    @if ($render->error_message)
        <p class="mt-2 text-4xs text-red-500">{{ $render->error_message }}</p>
    @endif

    @if ($render->isComplete() && $render->playbackUrl())
        <x-button
            class="mt-3 w-full"
            variant="outline"
            size="sm"
            href="{{ $render->playbackUrl() }}"
            target="_blank"
            rel="noreferrer"
        >
            <x-tabler-download class="size-3.5" />
            {{ __('Open video') }}
        </x-button>
    @endif
</div>
