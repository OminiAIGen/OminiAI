@extends('panel.layout.app', [
    'disable_tblr' => true,
    'disable_header' => true,
    'disable_footer' => true,
    'disable_titlebar' => true,
    'disable_navbar' => true,
    'layout_wide' => true,
    'disable_mobile_bottom_menu' => true,
])

@section('title', $title ?? __('Campaign'))

@php
    $agentEnabled = auth()->check()
        ? (new \App\Extensions\MarketingBot\System\Services\MarketingBotLimitService(auth()->user()))->isAgentEnabled()
        : false;
    $agentChannel = $agentChannel ?? 'whatsapp';
@endphp

@section('content')
    <div
        class="flex h-[calc(100dvh-1px)] flex-col"
        x-data="marketingAgentData()"
        x-init="initCopilot()"
    >
        {{-- Top chrome --}}
        <header class="flex shrink-0 items-center justify-between gap-4 border-b border-border bg-background px-4 py-3">
            <div class="flex items-center gap-3">
                <x-button
                    class="size-9 shrink-0 p-0"
                    variant="outline"
                    href="{{ route('dashboard.user.marketing-bot.dashboard') }}"
                >
                    <x-tabler-arrow-left class="size-4 rtl:rotate-180" />
                </x-button>
                <span class="hidden text-sm font-semibold text-heading-foreground sm:inline">{{ config('app.name') }}</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-heading-foreground">{{ $title ?? '' }}</span>
                <x-badge>{{ __('Draft') }}</x-badge>
            </div>

            <div class="flex items-center gap-2">
                @if (! ($app_is_demo ?? false))
                    <x-button
                        variant="primary"
                        type="button"
                        @click="saveAsDraft()"
                    >
                        {{ __('Save as Draft') }}
                    </x-button>
                @endif
            </div>
        </header>

        @if ($app_is_demo ?? false)
            <div class="flex shrink-0 items-center justify-center gap-2 border-b border-amber-500/20 bg-amber-500/10 px-4 py-2 text-2xs font-medium text-amber-600 dark:text-amber-400">
                <x-tabler-alert-triangle class="size-4 shrink-0" />
                {{ __('Demo mode: the AI agent is preview-only. Creating, saving, and sending campaigns are disabled.') }}
            </div>
        @endif

        {{-- Body: copilot | builder --}}
        <div class="relative flex min-h-0 flex-1">
            @if ($agentEnabled)
                @include('marketing-bot::agent.partials.copilot-panel')
            @endif

            <div class="flex min-h-0 flex-1 overflow-y-auto lg:overflow-hidden">
                @yield('builder')
            </div>
        </div>
    </div>

    <script>
        window.__marketingAgent = {
            channel: @json($agentChannel),
            campaignId: @json(optional($item ?? null)->id),
            initMessage: @json(request('copilot_init')),
            agentEnabled: @json($agentEnabled),
        };
    </script>

    @include('marketing-bot::agent.partials.copilot-scripts')
@endsection
