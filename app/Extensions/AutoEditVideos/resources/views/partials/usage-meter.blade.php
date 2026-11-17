@php
    $meters = [
        [
            'label' => __('Seconds rendered'),
            'used' => $usage['seconds_used'],
            'limit' => $usage['seconds_limit'],
            'unlimited' => $usage['seconds_unlimited'],
        ],
        [
            'label' => __('Videos'),
            'used' => $usage['renders_used'],
            'limit' => $usage['renders_limit'],
            'unlimited' => $usage['renders_unlimited'],
        ],
    ];
@endphp

<x-card
    class="lqd-auto-edit-usage"
    size="sm"
>
    <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
        <p class="text-label text-4xs uppercase tracking-wide">{{ __('This month') }}</p>

        @foreach ($meters as $meter)
            @php
                $percent = $meter['unlimited'] || $meter['limit'] <= 0 ? 0 : min(100, (int) round(($meter['used'] / $meter['limit']) * 100));
            @endphp

            <div class="min-w-32 flex-1">
                <div class="mb-1.5 flex items-baseline justify-between gap-2">
                    <span class="text-2xs font-medium">{{ $meter['label'] }}</span>
                    <span class="text-label text-4xs">
                        @if ($meter['unlimited'])
                            {{ __('Unlimited') }}
                        @else
                            {{ $meter['used'] }} / {{ $meter['limit'] }}
                        @endif
                    </span>
                </div>

                <div class="bg-foreground/10 h-1 w-full overflow-hidden rounded-full">
                    <div
                        @class([
                            'h-full rounded-full transition-all',
                            'bg-foreground/20' => $meter['unlimited'],
                            'bg-primary' => !$meter['unlimited'] && $percent < 90,
                            'bg-red-500' => !$meter['unlimited'] && $percent >= 90,
                        ])
                        style="width: {{ $meter['unlimited'] ? 100 : $percent }}%"
                    ></div>
                </div>
            </div>
        @endforeach
    </div>
</x-card>
