{{--
    Shared tail for every picker: the catalog-syncing hint, the empty state, the
    spinner and the x-intersect sentinel that pulls the next page.

    This replaces the old full-page reload banner — inside a modal, reloading the
    document would throw away whatever the user was composing.
--}}
<div
    class="rounded-card border border-dashed border-card-border p-4 text-center text-2xs text-label"
    x-cloak
    x-show="syncing"
>
    <span class="inline-flex items-center gap-2">
        <x-tabler-refresh class="size-4 animate-spin" />
        {{ __('Fetching the latest catalog.') }}
    </span>
    <button
        class="ms-2 underline underline-offset-2 transition hover:text-foreground"
        type="button"
        @click="reload()"
    >{{ __('Check again') }}</button>
</div>

<div
    class="py-14 text-center"
    x-cloak
    x-show="!loading && !syncing && items.length === 0"
>
    <x-tabler-mood-empty class="mx-auto mb-3 size-8 text-label" />
    <p class="m-0 text-2xs text-label">{{ $empty }}</p>

    @if (auth()->user()?->isAdmin() && Route::has('dashboard.admin.video-agent.settings'))
        <a
            class="mt-3 inline-block text-2xs text-primary underline underline-offset-2"
            href="{{ route('dashboard.admin.video-agent.settings') }}"
        >{{ __('Check video settings') }}</a>
    @endif
</div>

<p
    class="m-0 flex items-center justify-center gap-2 p-5 text-center text-2xs font-medium text-label"
    x-show="loading"
>
    <x-tabler-loader-2 class="size-4 animate-spin" />
    {{ __('Loading') }}
</p>

<div
    class="h-1"
    x-show="hasMore"
    x-intersect="loadMore()"
></div>
