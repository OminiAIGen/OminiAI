{{--
    Shown while SyncHeyGenCatalogJob mirrors the HeyGen catalog in the
    background. Reloads a bounded number of times so a stalled worker never
    leaves the page refreshing forever.
--}}
@if ($catalogSyncing ?? false)
    <div
        x-data="{ tries: Number(sessionStorage.getItem('videoAgentCatalogTries') || 0) }"
        x-init="
            if (tries < 10) {
                sessionStorage.setItem('videoAgentCatalogTries', tries + 1);
                setTimeout(() => window.location.reload(), 6000);
            }
        "
    >
        <x-alert variant="info-fill" icon="tabler-refresh" size="sm">
            {{ __('Fetching the latest catalog. This page refreshes itself when it is ready.') }}
        </x-alert>
    </div>
@else
    <div x-data x-init="sessionStorage.removeItem('videoAgentCatalogTries')" class="hidden"></div>
@endif
