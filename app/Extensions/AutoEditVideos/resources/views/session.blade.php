@extends('panel.layout.app', [
    'disable_titlebar' => true,
    'layout_wide' => true,
    'disable_footer' => true,
    'disable_mobile_bottom_menu' => true,
])
@section('title', $session->displayTitle())

@section('content')
    <div
        class="flex h-[calc(100dvh-var(--header-height,4.8125rem))] flex-col overflow-hidden lg:flex-row"
        x-data="autoEditThread({{ Js::from([
            'payload' => [
                'uuid' => $session->uuid,
                'title' => $session->displayTitle(),
                'status' => $session->status->value,
                'label' => $session->status->label(),
                'variant' => $session->status->badgeVariant(),
                'terminal' => $session->status->isTerminal(),
                'needs_user' => $session->status->needsUser(),
                'error' => $session->error_message,
                'rendley_project_id' => $session->rendley_project_id,
                'messages' => $session->messages->map(fn ($message) => [
                    'id' => $message->id,
                    'role' => $message->isFromUser() ? 'user' : 'agent',
                    'body' => $message->body,
                    'at' => $message->created_at?->diffForHumans(),
                ])->values(),
                'renders' => $session->renders->map(fn ($render) => [
                    'uuid' => $render->uuid,
                    'title' => $render->displayTitle(),
                    'status' => $render->status->value,
                    'label' => $render->status->label(),
                    'variant' => $render->status->badgeVariant(),
                    'terminal' => $render->isTerminal(),
                    'url' => $render->playbackUrl(),
                    'thumbnail' => $render->thumbnail_url,
                    'duration' => $render->formattedDuration(),
                    'error' => $render->error_message,
                ])->values(),
                'sources' => $session->sources->map(fn ($source) => [
                    'name' => $source->displayName(),
                    'duration' => $source->duration_seconds ? sprintf('%02d:%02d', intdiv($source->duration_seconds, 60), $source->duration_seconds % 60) : null,
                    'meta' => collect([
                        $source->width && $source->height ? $source->width.'×'.$source->height : null,
                        $source->width && $source->height ? ($source->height > $source->width ? '9:16' : ($source->width > $source->height ? '16:9' : '1:1')) : null,
                    ])->filter()->join(' · '),
                ])->values(),
                'target_resolution' => $session->target_resolution,
                'aspect_ratio' => $session->aspect_ratio,
            ],
            'sessions' => $sessions,
            'urls' => [
                'check' => route('dashboard.user.auto-edit-videos.sessions.check', $session),
                'message' => route('dashboard.user.auto-edit-videos.sessions.message', $session),
                'cost' => route('dashboard.user.auto-edit-videos.sessions.cost', $session),
                'export' => route('dashboard.user.auto-edit-videos.sessions.export', $session),
                'destroy' => route('dashboard.user.auto-edit-videos.sessions.destroy', $session),
            ],
            'usage' => $usage,
            'demo' => $isDemo,
        ]) }})"
    >
        @include('auto-edit-videos::partials.session-rail')

        <div class="flex min-h-0 min-w-0 grow flex-col bg-background">
            {{-- Thread header --}}
            <div class="flex shrink-0 items-center gap-3 border-b border-card-border bg-card-background/80 px-4 py-3 backdrop-blur-md lg:px-8">
                <span class="hidden size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary sm:flex">
                    <x-tabler-wand class="size-5" />
                </span>

                <div class="min-w-0 grow">
                    <p class="m-0 text-[10px] font-semibold uppercase tracking-[0.12em] text-label">
                        {{ __('Auto edit session') }}
                    </p>

                    <div class="mt-0.5 flex min-w-0 items-center gap-2.5">
                        <h3
                            class="mb-0 min-w-0 truncate text-base font-semibold"
                            x-text="title"
                        >{{ $session->displayTitle() }}</h3>

                        <x-badge
                            class="shrink-0 gap-1.5 px-2.5 py-1 text-[11px] hover:translate-y-0 hover:shadow-none"
                            ::class="`badge-${variant}`"
                            aria-live="polite"
                        >
                            <x-tabler-loader-2
                                class="size-3.5 animate-spin"
                                x-cloak
                                x-show="status === 'planning' || status === 'rendering'"
                            />
                            <x-tabler-sparkles
                                class="size-3.5"
                                x-cloak
                                x-show="status === 'ready'"
                            />
                            <x-tabler-circle-check
                                class="size-3.5"
                                x-cloak
                                x-show="status === 'completed'"
                            />
                            <x-tabler-alert-triangle
                                class="size-3.5"
                                x-cloak
                                x-show="status === 'failed'"
                            />
                            <x-tabler-circle-x
                                class="size-3.5"
                                x-cloak
                                x-show="status === 'canceled'"
                            />
                            <span x-text="label">{{ $session->status->label() }}</span>
                        </x-badge>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    {{-- Compact usage meter --}}
                    <span class="max-lg:hidden flex items-center gap-2 text-[11px] text-label">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-card-border bg-background px-2.5 py-1.5">
                            <x-tabler-clock class="size-3" />
                            @if ($usage['seconds_unlimited'])
                                {{ __('Unlimited') }}
                            @else
                                {{ $usage['seconds_used'] }}s / {{ $usage['seconds_limit'] }}s
                            @endif
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-card-border bg-background px-2.5 py-1.5">
                            <x-tabler-movie class="size-3" />
                            @if ($usage['renders_unlimited'])
                                {{ __('Unlimited') }}
                            @else
                                {{ $usage['renders_used'] }} / {{ $usage['renders_limit'] }}
                            @endif
                        </span>
                    </span>

                    <x-dropdown.dropdown
                        class:dropdown="w-52 p-2"
                        trigger-type="click"
                        anchor="end"
                        offset-y="8px"
                    >
                        <x-slot:trigger
                            class="size-9 rounded-full border border-input-border p-0"
                            variant="ghost"
                        >
                            <x-tabler-dots-vertical class="size-4" />
                            <span class="sr-only">{{ __('Session actions') }}</span>
                        </x-slot:trigger>

                        <x-slot:dropdown>
                            <ul class="m-0 flex list-none flex-col gap-0.5 p-0 [&_button]:flex [&_button]:w-full [&_button]:items-center [&_button]:gap-2.5 [&_button]:rounded-dropdown [&_button]:px-2.5 [&_button]:py-2 [&_button]:text-2xs [&_button:hover]:bg-foreground/5">
                                <li>
                                    <button
                                        class="text-rose-500"
                                        type="button"
                                        @click="removeSession({ uuid: uuid, url: urls.destroy })"
                                    >
                                        <x-tabler-trash class="size-4 shrink-0" />
                                        {{ __('Delete session') }}
                                    </button>
                                </li>
                            </ul>
                        </x-slot:dropdown>
                    </x-dropdown.dropdown>
                </div>
            </div>

            {{-- Scroll frame --}}
            <div
                class="flex min-h-0 grow flex-col overflow-y-auto bg-foreground/[0.015] px-3 sm:px-4 lg:px-8"
                x-ref="thread"
            >
                <div class="mx-auto mt-auto flex w-full max-w-4xl flex-col gap-6 py-6 lg:py-8">
                    {{-- Source files first: they are the input context, not the output.
                         Previously rendered after renders, they pushed the working
                         indicator off-screen and read as a result. --}}
                    <template x-if="sources.length">
                        <div class="flex gap-3 sm:gap-4">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary shadow-sm">
                                <x-tabler-files class="size-4" />
                            </span>

                            <div class="min-w-0 grow overflow-hidden rounded-[20px] border border-card-border bg-card-background shadow-sm shadow-black/[0.025]">
                                <div class="flex items-center justify-between gap-3 border-b border-card-border bg-foreground/[0.025] px-4 py-3 sm:px-5">
                                    <span class="inline-flex items-center gap-2 text-2xs font-semibold">
                                        {{ __('Source files') }}
                                    </span>
                                    <span class="rounded-full bg-foreground/5 px-2 py-1 text-[10px] font-medium text-label" x-text="`${sources.length} {{ __('files') }}`"></span>
                                </div>

                                <ul class="m-0 flex list-none flex-col p-0">
                                    <template
                                        x-for="(source, index) in sources"
                                        :key="index"
                                    >
                                        <li class="flex items-center gap-2.5 border-b border-card-border px-4 py-3 last:border-b-0 sm:px-5">
                                            <span class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-foreground/5 text-label">
                                                <x-tabler-file class="size-3.5" />
                                            </span>
                                            <span class="min-w-0 flex-1 truncate text-2xs" x-text="source.name"></span>
                                            <span
                                                class="shrink-0 text-[11px] text-label"
                                                x-show="source.meta"
                                                x-text="source.meta"
                                            ></span>
                                            <span
                                                class="shrink-0 text-[11px] text-label"
                                                x-show="source.duration"
                                                x-text="source.duration"
                                            ></span>
                                        </li>
                                    </template>
                                </ul>

                                {{-- Chosen export settings — labelled as such so they are
                                     never mistaken for the source file's own size. --}}
                                <div class="flex flex-wrap items-center gap-2 border-t border-card-border bg-foreground/[0.025] px-4 py-3 text-[11px] text-label sm:px-5">
                                    <span class="inline-flex items-center gap-1.5 font-medium text-foreground">
                                        <x-tabler-adjustments-horizontal class="size-3.5" />
                                        {{ __('Export settings') }}
                                    </span>
                                    <span class="rounded-full border border-card-border bg-background px-2 py-0.5">{{ $session->target_resolution }}</span>
                                    @if ($session->aspect_ratio)
                                        <span class="rounded-full border border-card-border bg-background px-2 py-0.5">{{ $session->aspect_ratio }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Messages --}}
                    <template
                        x-for="message in messages"
                        :key="message.id"
                    >
                        <div
                            class="flex gap-3 sm:gap-4 [&.is-user]:flex-row-reverse"
                            :class="{ 'is-user': message.role === 'user' }"
                        >
                            <span
                                class="flex size-9 shrink-0 items-center justify-center rounded-xl border border-card-border bg-card-background text-label shadow-sm [&.is-user]:border-primary/10 [&.is-user]:bg-primary/10 [&.is-user]:text-primary"
                                :class="{ 'is-user': message.role === 'user' }"
                            >
                                <x-tabler-user
                                    class="size-4"
                                    x-show="message.role === 'user'"
                                />
                                <x-tabler-scissors
                                    class="size-4"
                                    x-show="message.role !== 'user'"
                                />
                            </span>

                            <div
                                class="flex max-w-[85%] flex-col items-start gap-1.5 [&.is-user]:items-end"
                                :class="{ 'is-user': message.role === 'user' }"
                            >
                                <span
                                    class="px-1 text-[10px] font-semibold uppercase tracking-wide text-label"
                                    x-text="message.role === 'user' ? {{ Js::from(__('You')) }} : {{ Js::from(__('AI editor')) }}"
                                ></span>
                                <div
                                    class="whitespace-pre-line rounded-[18px] rounded-ss-md border border-card-border bg-card-background px-4 py-3 text-2xs leading-relaxed shadow-sm shadow-black/[0.025] [&.is-user]:rounded-se-md [&.is-user]:rounded-ss-[18px] [&.is-user]:border-primary/10 [&.is-user]:bg-primary/10"
                                    :class="{ 'is-user': message.role === 'user' }"
                                    x-text="message.body"
                                ></div>
                                <div
                                    class="px-1 text-[10px] text-label"
                                    x-show="message.at"
                                    x-text="message.at"
                                ></div>
                            </div>
                        </div>
                    </template>

                    {{-- Renders --}}
                    @php
                        $sessionVideoClass = match ($session->aspect_ratio) {
                            '9:16'  => 'aspect-[9/16] mx-auto max-h-[520px] w-auto',
                            '1:1'   => 'aspect-square mx-auto max-h-[520px] w-auto',
                            '4:5'   => 'aspect-[4/5] mx-auto max-h-[520px] w-auto',
                            default => 'aspect-video w-full',
                        };
                    @endphp
                    <template
                        x-for="render in renders"
                        :key="render.uuid"
                    >
                        <div class="flex gap-3 sm:gap-4">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary shadow-sm">
                                <x-tabler-movie class="size-4" />
                            </span>

                            <div class="min-w-0 grow overflow-hidden rounded-[20px] border border-card-border bg-card-background shadow-md shadow-black/[0.035]">
                                <div class="flex min-h-52 items-center justify-center overflow-hidden bg-black/95">
                                    <template x-if="render.url">
                                        <video
                                            class="{{ $sessionVideoClass }} bg-black object-contain"
                                            :src="render.url"
                                            :poster="render.thumbnail"
                                            controls
                                            preload="metadata"
                                        ></video>
                                    </template>

                                    <template x-if="!render.url && !render.terminal">
                                        <div class="flex {{ $sessionVideoClass }} min-h-52 items-center justify-center bg-foreground/5">
                                            <span class="inline-flex items-center gap-2.5 rounded-full bg-background px-3 py-2 text-2xs text-label shadow-sm">
                                                <x-tabler-loader-2 class="size-4 animate-spin text-primary" />
                                                <span x-text="render.label"></span>
                                            </span>
                                        </div>
                                    </template>

                                    <template x-if="!render.url && render.terminal">
                                        <div
                                            class="flex {{ $sessionVideoClass }} min-h-52 items-center justify-center"
                                            :class="render.status === 'failed' ? 'bg-rose-500/5' : 'bg-foreground/5'"
                                        >
                                            <span
                                                class="inline-flex items-center gap-2.5 rounded-full bg-background px-3 py-2 text-2xs shadow-sm"
                                                :class="render.status === 'failed' ? 'text-rose-500' : 'text-label'"
                                            >
                                                <x-tabler-alert-triangle
                                                    class="size-4"
                                                    x-show="render.status === 'failed'"
                                                />
                                                <x-tabler-circle-x
                                                    class="size-4"
                                                    x-show="render.status === 'canceled'"
                                                />
                                                <span x-text="render.status === 'failed' ? {{ Js::from(__('Render failed')) }} : render.label"></span>
                                            </span>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex flex-wrap items-center gap-3 border-t border-card-border px-4 py-3.5 sm:px-5">
                                    <div class="min-w-0 grow">
                                        <span class="block truncate text-2xs font-semibold" x-text="render.title"></span>
                                        <span class="mt-0.5 flex items-center gap-1.5 text-[10px] text-label">
                                            <x-tabler-player-play class="size-3" />
                                            <span x-text="render.url ? {{ Js::from(__('Rendered video')) }} : {{ Js::from(__('Video render')) }}"></span>
                                            <template x-if="render.duration">
                                                <span class="contents">
                                                    <span aria-hidden="true">&middot;</span>
                                                    <span x-text="render.duration"></span>
                                                </span>
                                            </template>
                                        </span>
                                    </div>

                                    <x-badge
                                        class="shrink-0 gap-1.5 px-2.5 py-1 text-[10px] hover:translate-y-0 hover:shadow-none"
                                        ::class="`badge-${render.variant}`"
                                    >
                                        <x-tabler-loader-2
                                            class="size-3 animate-spin"
                                            x-cloak
                                            x-show="!render.terminal"
                                        />
                                        <x-tabler-circle-check
                                            class="size-3"
                                            x-cloak
                                            x-show="render.status === 'completed'"
                                        />
                                        <x-tabler-alert-triangle
                                            class="size-3"
                                            x-cloak
                                            x-show="render.status === 'failed'"
                                        />
                                        <x-tabler-circle-x
                                            class="size-3"
                                            x-cloak
                                            x-show="render.status === 'canceled'"
                                        />
                                        <span x-text="render.label"></span>
                                    </x-badge>

                                    <template x-if="render.url">
                                        <a
                                            class="inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-button bg-primary px-3.5 text-xs font-medium text-primary-foreground transition-all hover:-translate-y-0.5 hover:bg-primary/90 hover:shadow-xl hover:shadow-primary/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                                            :href="render.url"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            <x-tabler-download class="size-3.5" />
                                            {{ __('Download video') }}
                                        </a>
                                    </template>
                                </div>

                                <p
                                    class="m-0 px-4 pb-3 text-[11px] text-rose-500"
                                    x-show="render.error"
                                    x-text="render.error"
                                ></p>
                            </div>
                        </div>
                    </template>

                    {{-- Export --}}
                    <div
                        class="flex gap-3 sm:gap-4"
                        x-cloak
                        x-show="status === 'ready'"
                    >
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-orange-500/10 text-orange-500 shadow-sm">
                            <x-tabler-sparkles class="size-4" />
                        </span>

                        <div class="min-w-0 grow rounded-[20px] border border-orange-500/20 bg-orange-500/[0.035] px-4 py-4 shadow-sm sm:px-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="m-0 text-2xs font-semibold">{{ __('Ready to render?') }}</p>
                                <x-badge class="gap-1 py-1 text-[10px] hover:translate-y-0 hover:shadow-none" variant="warning">
                                    <x-tabler-clock class="size-3" />
                                    {{ __('Action required') }}
                                </x-badge>
                            </div>

                            <p class="m-0 mt-1 text-[11px] leading-relaxed text-label">
                                {{ __('Check the price, then export. You are only charged once the render finishes.') }}
                            </p>

                            <div
                                class="mt-2.5 text-[11px] text-rose-500"
                                x-cloak
                                x-show="costError"
                                x-text="costError"
                            ></div>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <template x-if="credits === null">
                                    <x-button
                                        type="button"
                                        size="sm"
                                        @click="fetchCost()"
                                        ::disabled="busy || costLoading || demo"
                                    >
                                        <x-tabler-loader-2
                                            class="size-3.5 animate-spin"
                                            x-cloak
                                            x-show="costLoading"
                                        />
                                        <x-tabler-sparkles
                                            class="size-3.5"
                                            x-show="!costLoading"
                                        />
                                        <span x-show="!costLoading">{{ __('Export video') }}</span>
                                        <span x-show="costLoading">{{ __('Checking price…') }}</span>
                                    </x-button>
                                </template>

                                <template x-if="credits !== null">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="text-2xs font-medium"
                                            x-text="exportPriceLabel"
                                        ></span>

                                        <x-button
                                            type="button"
                                            size="sm"
                                            @click="exportVideo()"
                                            ::disabled="busy || demo"
                                        >
                                            <x-tabler-loader-2
                                                class="size-3.5 animate-spin"
                                                x-cloak
                                                x-show="busy"
                                            />
                                            {{ __('Confirm export') }}
                                        </x-button>

                                        <button
                                            class="text-[11px] text-label underline-offset-2 hover:underline"
                                            type="button"
                                            @click="credits = null; costError = ''"
                                        >{{ __('Cancel') }}</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Error --}}
                    <div
                        class="flex gap-3 sm:gap-4"
                        x-cloak
                        x-show="error"
                    >
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-500 shadow-sm">
                            <x-tabler-alert-triangle class="size-4" />
                        </span>

                        <div class="min-w-0 grow rounded-[20px] border border-rose-500/20 bg-rose-500/5 px-4 py-4 shadow-sm sm:px-5">
                            <p class="m-0 text-2xs font-medium text-rose-500">{{ __('This edit could not be finished.') }}</p>

                            <p
                                class="m-0 mt-1 whitespace-pre-line text-[11px] leading-relaxed text-rose-500/85"
                                x-text="error"
                            ></p>

                            <a
                                class="mt-2.5 inline-flex items-center gap-1.5 text-[11px] font-medium text-rose-500 underline-offset-2 hover:underline"
                                href="{{ route('dashboard.user.auto-edit-videos.index') }}"
                            >
                                <x-tabler-refresh class="size-3.5" />
                                {{ __('Start a new edit') }}
                            </a>
                        </div>
                    </div>

                    {{-- Working indicator: status-aware so a render never reads as planning. --}}
                    <div
                        class="flex items-center gap-3 sm:gap-4"
                        x-cloak
                        x-show="working"
                    >
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary shadow-sm">
                            <x-tabler-loader-2 class="size-4 animate-spin" />
                        </span>

                        <span class="rounded-full border border-card-border bg-card-background px-3 py-2 text-2xs text-label shadow-sm">
                            <x-shimmer-text><span x-text="workingLabel"></span></x-shimmer-text>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Reply composer --}}
            <div class="shrink-0 bg-gradient-to-t from-background via-background to-transparent px-3 pb-4 pt-3 sm:px-4 lg:px-8 lg:pb-5 lg:pt-4">
                <div class="mx-auto w-full max-w-4xl">
                    <form
                        class="rounded-[24px] border border-card-border bg-card-background p-3 shadow-xl shadow-black/[0.06] transition focus-within:border-primary/40 focus-within:shadow-primary/5 focus-within:ring-2 focus-within:ring-primary/10"
                        @submit.prevent="send()"
                    >
                        <div class="flex items-center justify-between gap-3 px-1">
                            <label
                                class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-foreground"
                                for="auto-edit-reply"
                            >
                                <x-tabler-sparkles class="size-3.5 text-primary" />
                                {{ __('Ask for changes') }}
                            </label>
                            <span
                                class="text-[10px] tabular-nums text-label"
                                x-text="`${message.length} / 4000`"
                            ></span>
                        </div>

                        <textarea
                            class="mt-1 max-h-48 min-h-[52px] w-full resize-none border-none bg-transparent px-1 py-2 text-sm leading-relaxed text-foreground placeholder:text-label focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50"
                            id="auto-edit-reply"
                            rows="2"
                            maxlength="4000"
                            placeholder="{{ __('Make it shorter, swap the music for something calmer.') }}"
                            x-ref="reply"
                            x-model="message"
                            @input="autogrow()"
                            @keydown.enter="if (! $event.shiftKey) { $event.preventDefault(); send(); }"
                            :disabled="terminal"
                        ></textarea>

                        <div class="flex items-center justify-between gap-3 border-t border-card-border px-1 pt-2">
                            <span
                                class="inline-flex items-center gap-1.5 text-[10px] text-label"
                                x-cloak
                                x-show="!terminal"
                            >
                                <x-tabler-corner-down-left class="size-3" />
                                {{ __('Enter sends · Shift + Enter adds a line') }}
                            </span>
                            <span
                                class="text-[10px] text-label"
                                x-cloak
                                x-show="terminal"
                            >{{ __('This edit is closed.') }}</span>

                            <x-button
                                class="size-10 shrink-0 rounded-full p-0"
                                type="submit"
                                size="none"
                                ::disabled="!canSend"
                            >
                                <x-tabler-loader-2
                                    class="size-4 animate-spin"
                                    x-cloak
                                    x-show="busy"
                                />
                                <x-tabler-arrow-up
                                    class="size-4"
                                    x-show="!busy"
                                />
                                <span class="sr-only">{{ __('Send') }}</span>
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('autoEditThread', (initial) => ({
                    uuid: initial.payload.uuid,
                    title: initial.payload.title,
                    status: initial.payload.status,
                    label: initial.payload.label,
                    variant: initial.payload.variant,
                    needsUser: initial.payload.needs_user,
                    terminal: initial.payload.terminal,
                    error: initial.payload.error,
                    rendleyProjectId: initial.payload.rendley_project_id,
                    messages: initial.payload.messages,
                    renders: initial.payload.renders,
                    sources: initial.payload.sources,
                    targetResolution: initial.payload.target_resolution,
                    aspectRatio: initial.payload.aspect_ratio,
                    sessions: initial.sessions || [],
                    urls: initial.urls,
                    usage: initial.usage || {},
                    demo: initial.demo,
                    message: '',
                    busy: false,
                    credits: null,
                    costLoading: false,
                    costError: '',
                    timer: null,

                    init() {
                        this.$nextTick(() => {
                            this.autogrow();
                            this.scrollToEnd();
                        });
                        this.startPolling();
                    },

                    destroy() {
                        clearInterval(this.timer);
                    },

                    startPolling() {
                        clearInterval(this.timer);

                        if (!this.working && !this.hasUnfinishedRenders) {
                            return;
                        }

                        this.timer = setInterval(() => this.poll(), 5000);
                    },

                    get working() {
                        return this.status === 'planning' || this.status === 'rendering';
                    },

                    get workingLabel() {
                        if (this.status === 'rendering') {
                            return {{ Js::from(__('Rendering…')) }};
                        }

                        return {{ Js::from(__('Planning the edit…')) }};
                    },

                    get hasUnfinishedRenders() {
                        return (this.renders || []).some((render) => !render.terminal);
                    },

                    get canSend() {
                        return !this.busy && !this.demo && this.message.trim().length > 0 && !this.terminal;
                    },

                    get exportPriceLabel() {
                        if (this.credits === null) {
                            return '';
                        }

                        return {{ Js::from(__('This export will cost :credits credits.')) }}.replace(':credits', this.credits);
                    },

                    apply(data) {
                        this.title = data.title ?? this.title;
                        this.status = data.status;
                        this.label = data.label;
                        this.variant = data.variant;
                        this.needsUser = data.needs_user;
                        this.terminal = data.terminal;
                        this.error = data.error;
                        this.messages = data.messages || [];
                        this.renders = data.renders || [];

                        if (this.status !== 'ready') {
                            this.credits = null;
                            this.costError = '';
                        }

                        if (!this.working && !this.hasUnfinishedRenders) {
                            clearInterval(this.timer);
                        } else if (!this.timer) {
                            this.startPolling();
                        }

                        this.$nextTick(() => this.scrollToEnd());
                    },

                    scrollToEnd() {
                        const el = this.$refs.thread;

                        if (el) {
                            el.scrollTop = el.scrollHeight;
                        }
                    },

                    autogrow() {
                        const el = this.$refs.reply;

                        if (!el) {
                            return;
                        }

                        el.style.height = 'auto';
                        el.style.height = Math.min(el.scrollHeight, 200) + 'px';
                    },

                    async poll() {
                        if (document.hidden) {
                            return;
                        }

                        try {
                            const response = await fetch(this.urls.check, {
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            });

                            if (!response.ok) {
                                return;
                            }

                            const data = await response.json();
                            this.apply(data);
                        } catch (error) {
                            // Transient network error: the next tick retries.
                        }
                    },

                    async send() {
                        if (!this.canSend) {
                            return;
                        }

                        const body = this.message.trim();

                        // Optimistic echo so the thread doesn't sit empty while
                        // the editor thinks about it.
                        this.messages.push({ id: 'pending-' + Date.now(), role: 'user', body, at: null });
                        this.message = '';
                        this.$nextTick(() => {
                            this.autogrow();
                            this.scrollToEnd();
                        });

                        this.busy = true;

                        try {
                            const response = await fetch(this.urls.message, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ body }),
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                this.error = data.message || '';
                                return;
                            }

                            if (data.data) {
                                this.status = data.data.status;
                                this.label = data.data.label;
                                this.variant = data.data.variant;
                                this.terminal = data.data.terminal;
                                this.messages = data.data.messages || [];

                                this.startPolling();
                            }

                            this.$nextTick(() => this.scrollToEnd());
                        } catch (error) {
                            this.error = {{ Js::from(__('Could not reach the server.')) }};
                        } finally {
                            this.busy = false;
                        }
                    },

                    async fetchCost() {
                        this.costLoading = true;
                        this.costError = '';

                        try {
                            const response = await fetch(this.urls.cost, {
                                headers: { 'Accept': 'application/json' },
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                this.costError = data.message ?? '';
                                return;
                            }

                            this.credits = data.credits;
                        } catch (error) {
                            this.costError = {{ Js::from(__('Could not reach the server.')) }};
                        } finally {
                            this.costLoading = false;
                        }
                    },

                    async exportVideo() {
                        this.busy = true;

                        try {
                            const response = await fetch(this.urls.export, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                this.error = data.message || '';
                                return;
                            }

                            this.credits = null;
                            this.costError = '';
                            this.status = 'rendering';
                            this.label = {{ Js::from(__('Rendering')) }};
                            this.startPolling();
                            this.poll();
                        } catch (error) {
                            this.error = {{ Js::from(__('Could not reach the server.')) }};
                        } finally {
                            this.busy = false;
                        }
                    },

                    async removeSession(session) {
                        if (!confirm({{ Js::from(__('Delete permanently?')  ) }})) {
                            return;
                        }

                        try {
                            await fetch(session.url, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (session.uuid === this.uuid) {
                                window.location.href = {{ Js::from(route('dashboard.user.auto-edit-videos.index')) }};

                                return;
                            }

                            this.sessions = this.sessions.filter((item) => item.uuid !== session.uuid);
                        } catch (error) {
                            // Transient network error.
                        }
                    },
                }));
            });
        </script>
    @endpush
@endsection
