{{--
    Compact usage chip. Reads the six-key array VideoAgentBillingService::usage()
    returns; -1 means unlimited.
--}}
@php
    $secondsUnlimited = $usage['seconds_unlimited'];
    $secondsPercent = $secondsUnlimited || $usage['seconds_limit'] <= 0
        ? 0
        : min(100, (int) round($usage['seconds_used'] / $usage['seconds_limit'] * 100));
@endphp

<div class="inline-flex flex-wrap items-center gap-x-4 gap-y-2 rounded-full border border-card-border bg-card-background px-4 py-2 text-[11px] text-label">
    <span class="inline-flex items-center gap-2">
        <x-tabler-clock class="size-3.5 shrink-0 opacity-60" />
        <span>
            <span class="font-medium text-foreground">{{ $usage['seconds_used'] }}</span>
            @if ($secondsUnlimited)
                {{ __('seconds rendered · unlimited') }}
            @else
                / {{ $usage['seconds_limit'] }} {{ __('seconds this month') }}
            @endif
        </span>
    </span>

    @unless ($secondsUnlimited)
        <span class="h-1.5 w-16 shrink-0 overflow-hidden rounded-full bg-foreground/10">
            <span
                class="block h-full rounded-full bg-primary transition-all"
                style="width: {{ $secondsPercent }}%"
            ></span>
        </span>
    @endunless

    <span class="inline-flex items-center gap-2">
        <x-tabler-movie class="size-3.5 shrink-0 opacity-60" />
        <span>
            <span class="font-medium text-foreground">{{ $usage['videos_used'] }}</span>
            @if ($usage['videos_unlimited'])
                {{ __('videos · unlimited') }}
            @else
                / {{ $usage['videos_limit'] }} {{ __('videos') }}
            @endif
        </span>
    </span>
</div>
