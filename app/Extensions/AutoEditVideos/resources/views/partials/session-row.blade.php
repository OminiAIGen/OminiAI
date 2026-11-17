@php
    $isBusy = $session->status->isAwaitingAgent() || $session->status === \App\Extensions\AutoEditVideos\System\Enums\SessionStatusEnum::Rendering;
@endphp

<a
    class="border-card-border hover:bg-foreground/5 flex items-center gap-3.5 border-b px-6 py-3.5 transition last:border-b-0"
    href="{{ route('dashboard.user.auto-edit-videos.sessions.show', $session) }}"
>
    <span
        @class([
            'flex size-9 shrink-0 items-center justify-center rounded-full',
            'bg-primary/10 text-primary' => $isBusy,
            'bg-foreground/5 text-label' => !$isBusy,
        ])
    >
        @if ($isBusy)
            <x-tabler-loader-2 class="size-4 animate-spin" />
        @elseif ($session->status->badgeVariant() === 'danger')
            <x-tabler-alert-triangle class="size-4" />
        @else
            <x-tabler-movie class="size-4" />
        @endif
    </span>

    <span class="min-w-0 flex-1">
        <span class="block truncate text-2xs font-medium">{{ $session->displayTitle() }}</span>
        <span class="text-label mt-0.5 block text-4xs">
            {{ optional($session->created_at)->diffForHumans() }}
            @if ($session->renders_count ?? false)
                &middot; {{ trans_choice('{1} :count render|[2,*] :count renders', $session->renders_count, ['count' => $session->renders_count]) }}
            @endif
        </span>
    </span>

    <x-badge
        class="shrink-0"
        variant="{{ $session->status->badgeVariant() }}"
    >
        {{ $session->status->label() }}
    </x-badge>
</a>
