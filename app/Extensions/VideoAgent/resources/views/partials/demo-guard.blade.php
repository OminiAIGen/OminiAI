@if ($app_is_demo)
    <div class="mb-4 flex items-center gap-2 rounded-card border border-primary/20 bg-primary/5 px-3 py-2 text-2xs text-label">
        <x-tabler-lock class="size-4 shrink-0 text-primary" />
        <span>{{ __('Demo mode lets you browse these options, but uploads and changes are disabled.') }}</span>
    </div>
@endif
