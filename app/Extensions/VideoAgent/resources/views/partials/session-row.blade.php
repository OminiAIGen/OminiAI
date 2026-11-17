{{-- One row in the landing page's recent list. --}}
<a
    class="flex items-center gap-4 rounded-card border border-card-border bg-card-background px-4 py-3 transition hover:border-primary"
    :href="session.url"
>
    <span
        class="size-2 shrink-0 rounded-full"
        :class="{
            'bg-emerald-500': session.status === 'completed',
            'bg-rose-500': session.status === 'failed',
            'bg-orange-500': session.needs_user,
            'bg-foreground/25': session.status === 'stopped',
            'animate-pulse bg-primary': !session.terminal && !session.needs_user,
        }"
    ></span>

    <span class="flex min-w-0 grow flex-col">
        <span
            class="truncate text-2xs font-medium"
            x-text="session.title"
        ></span>
        <span
            class="truncate text-[11px] text-label"
            x-text="session.at"
        ></span>
    </span>

    <x-badge
        class="shrink-0"
        ::class="`badge-${session.variant}`"
    >
        <span x-text="session.label"></span>
    </x-badge>
</a>
