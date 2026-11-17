{{--
    Chat workspace. The page owns the viewport: header stays, footer and the
    mobile bottom bar are dropped so the thread can scroll inside a fixed
    frame instead of pushing the document down.
--}}
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
        x-data="videoAgentThread({{ Js::from([
            'payload' => $payload,
            'sessions' => $sessions->map(fn ($item) => [
                'uuid' => $item->uuid,
                'title' => $item->displayTitle(),
                'status' => $item->status->value,
                'variant' => $item->status->badgeVariant(),
                'terminal' => $item->isTerminal(),
                'needs_user' => $item->status->needsUser(),
                'today' => (bool) $item->created_at?->isToday(),
                'url' => route('dashboard.user.video-agent.sessions.show', $item),
            ])->values(),
            'urls' => [
                'check' => route('dashboard.user.video-agent.sessions.check', $session),
                'message' => route('dashboard.user.video-agent.sessions.message', $session),
                'approve' => route('dashboard.user.video-agent.sessions.approve', $session),
                'stop' => route('dashboard.user.video-agent.sessions.stop', $session),
                'destroy' => route('dashboard.user.video-agent.sessions.destroy', $session),
            ],
        ]) }})"
    >
        @include('video-agent::partials.session-rail')

        <div class="flex min-h-0 min-w-0 grow flex-col bg-background">
            {{-- Thread header --}}
            <div class="flex shrink-0 items-center gap-3 border-b border-card-border bg-card-background/80 px-4 py-3 backdrop-blur-md lg:px-8">
                <span class="hidden size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary sm:flex">
                    <x-tabler-sparkles class="size-5" />
                </span>

                <div class="min-w-0 grow">
                    <p class="m-0 text-[10px] font-semibold uppercase tracking-[0.12em] text-label">
                        {{ __('Video agent session') }}
                    </p>

                    <div class="mt-0.5 flex min-w-0 items-center gap-2.5">
                        <h3
                            class="mb-0 min-w-0 truncate text-base font-semibold"
                            x-text="title"
                        >{{ $session->displayTitle() }}</h3>

                        <span
                            class="inline-flex h-6 shrink-0 items-center justify-center gap-1.5 rounded-full px-2 text-[11px] font-medium leading-none"
                            :class="{
                                'bg-sky-500/10 text-sky-600 dark:text-sky-400': variant === 'info',
                                'bg-orange-500/10 text-orange-600 dark:text-orange-400': variant === 'warning',
                                'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': variant === 'success',
                                'bg-rose-500/10 text-rose-600 dark:text-rose-400': variant === 'danger',
                                'bg-foreground/10 text-label': variant === 'secondary',
                            }"
                            aria-live="polite"
                        >
                            <x-tabler-loader-2
                                class="size-3.5 animate-spin"
                                x-cloak
                                x-show="status === 'thinking' || status === 'generating'"
                            />
                            <x-tabler-message-circle
                                class="size-3.5"
                                x-cloak
                                x-show="status === 'waiting_for_input'"
                            />
                            <x-tabler-clipboard-check
                                class="size-3.5"
                                x-cloak
                                x-show="status === 'reviewing'"
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
                            <x-tabler-player-stop
                                class="size-3.5"
                                x-cloak
                                x-show="status === 'stopped'"
                            />
                            <span x-text="label">{{ $session->status->label() }}</span>
                        </span>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2">
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
                            @if ($usage['videos_unlimited'])
                                {{ __('Unlimited') }}
                            @else
                                {{ $usage['videos_used'] }} / {{ $usage['videos_limit'] }}
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
                                        type="button"
                                        ::disabled="terminal || busy"
                                        @click="toggle('collapse'); stop()"
                                    >
                                        <x-tabler-player-stop class="size-4 shrink-0 opacity-70" />
                                        {{ __('Stop session') }}
                                    </button>
                                </li>
                                <li>
                                    <button
                                        class="text-rose-500"
                                        type="button"
                                        @click="toggle('collapse'); remove({ uuid: uuid, url: urls.destroy })"
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

            {{--
                The scroll frame is full width so the scrollbar sits at the edge,
                but everything inside it lives in a centred reading column —
                otherwise the bubbles fly to opposite ends of a 2000px screen.
                `mt-auto` keeps a short conversation pinned to the composer.
            --}}
            <div
                class="flex min-h-0 grow flex-col overflow-y-auto bg-foreground/[0.015] px-3 sm:px-4 lg:px-8"
                x-ref="thread"
            >
                <div class="mx-auto mt-auto flex w-full max-w-4xl flex-col gap-6 py-6 lg:py-8">
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
                                <x-tabler-sparkles
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
                                    x-text="message.role === 'user' ? {{ Js::from(__('You')) }} : {{ Js::from(__('Video agent')) }}"
                                ></span>
                                <div
                                    class="break-words whitespace-pre-line rounded-[18px] rounded-ss-md border border-card-border bg-card-background px-4 py-3 text-2xs leading-relaxed shadow-sm shadow-black/[0.025] [&.is-user]:rounded-se-md [&.is-user]:rounded-ss-[18px] [&.is-user]:border-primary/10 [&.is-user]:bg-primary/10"
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

                    {{-- Storyboard: an agent artefact card, inline in the conversation --}}
                    <template x-if="scenes.length">
                        <div class="flex gap-3 sm:gap-4">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary shadow-sm">
                                <x-tabler-layout-list class="size-4" />
                            </span>

                            <div class="min-w-0 grow overflow-hidden rounded-[20px] border border-card-border bg-card-background shadow-md shadow-black/[0.035]">
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-card-border bg-foreground/[0.025] px-4 py-3 sm:px-5">
                                    <span class="inline-flex items-center gap-2 text-2xs font-semibold">
                                        <x-tabler-list-details class="size-4 text-primary" />
                                        {{ __('Storyboard') }}
                                    </span>
                                    <span class="inline-flex items-center gap-2">
                                        <span
                                            class="rounded-full bg-foreground/5 px-2 py-1 text-[10px] font-medium text-label"
                                            x-text="`${scenes.length} {{ __('scenes') }}`"
                                        ></span>
                                        <x-badge
                                            class="gap-1 py-1 text-[10px] hover:translate-y-0 hover:shadow-none"
                                            variant="warning"
                                            x-cloak
                                            x-show="needsUser"
                                        >
                                            <x-tabler-clock class="size-3" />
                                            {{ __('Review required') }}
                                        </x-badge>
                                    </span>
                                </div>

                                <ol class="m-0 grid list-none gap-px bg-card-border p-0 sm:grid-cols-2">
                                    <template
                                        x-for="scene in scenes"
                                        :key="scene.index"
                                    >
                                        <li class="flex min-w-0 gap-3 bg-card-background p-4 sm:p-5">
                                            <span
                                                class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-[11px] font-semibold text-primary"
                                                x-text="scene.index"
                                            ></span>

                                            <div class="min-w-0 grow">
                                                <div class="flex items-start justify-between gap-3">
                                                    <span
                                                        class="min-w-0 break-words text-2xs font-semibold"
                                                        x-show="scene.title"
                                                        x-text="scene.title"
                                                    ></span>
                                                    <span
                                                        class="inline-flex shrink-0 items-center gap-1 rounded-full bg-foreground/5 px-2 py-0.5 text-[10px] text-label"
                                                        x-show="scene.duration"
                                                    >
                                                        <x-tabler-clock class="size-3" />
                                                        <span x-text="`${scene.duration}s`"></span>
                                                    </span>
                                                </div>

                                                <p
                                                    class="m-0 mt-1.5 break-words text-[11px] leading-relaxed text-label"
                                                    x-show="scene.visual"
                                                    x-text="scene.visual"
                                                ></p>

                                                <p
                                                    class="m-0 mt-2 break-words text-2xs leading-relaxed"
                                                    x-show="scene.narration"
                                                    x-text="scene.narration"
                                                ></p>

                                                <div
                                                    class="mt-2.5 flex items-start gap-2 rounded-xl border border-card-border bg-foreground/[0.025] px-3 py-2 text-[11px] text-label"
                                                    x-show="scene.on_screen_text"
                                                >
                                                    <x-tabler-text-caption class="mt-0.5 size-3.5 shrink-0" />
                                                    <span class="break-words" x-text="scene.on_screen_text"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </template>
                                </ol>

                                {{-- The approval moment lives with the plan it approves. --}}
                                <div
                                    class="flex flex-wrap items-center gap-3 border-t border-card-border bg-orange-500/[0.035] px-4 py-3.5 sm:px-5"
                                    x-cloak
                                    x-show="canApprove"
                                >
                                    <span class="min-w-52 grow text-[11px] leading-relaxed text-label">
                                        {{ __('Review the scenes before the agent starts rendering.') }}
                                    </span>
                                    <x-button
                                        type="button"
                                        size="sm"
                                        ::disabled="busy || demo"
                                        @click="approve()"
                                    >
                                        <x-tabler-check class="size-4" />
                                        {{ __('Approve & render') }}
                                    </x-button>

                                    <x-button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        ::disabled="busy"
                                        @click="stop()"
                                    >
                                        <x-tabler-player-stop class="size-3.5" />
                                        {{ __('Stop') }}
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div
                        class="flex gap-3 sm:gap-4"
                        x-cloak
                        x-show="canApprove && !scenes.length"
                    >
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary shadow-sm">
                            <x-tabler-clipboard-check class="size-4" />
                        </span>

                        <div class="min-w-0 grow rounded-[20px] border border-card-border bg-card-background px-4 py-4 shadow-sm sm:px-5">
                            <p class="m-0 text-2xs font-semibold">{{ __('Plan ready for review') }}</p>
                            <p class="m-0 mt-1 text-[11px] leading-relaxed text-label">
                                {{ __('Review the agent’s plan above. Approve it to start rendering, or reply with changes.') }}
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <x-button
                                    type="button"
                                    size="sm"
                                    ::disabled="busy || demo"
                                    @click="approve()"
                                >
                                    <x-tabler-check class="size-4" />
                                    {{ __('Approve & render') }}
                                </x-button>
                            </div>
                        </div>
                    </div>

                    {{-- Rendered output --}}
                    @php
                        $sessionVideoClass = $session->orientation === 'portrait'
                            ? 'aspect-[9/16] mx-auto max-h-[520px] w-auto'
                            : 'aspect-video w-full';
                    @endphp
                    <template
                        x-for="video in videos"
                        :key="video.uuid"
                    >
                        <div class="flex gap-3 sm:gap-4">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary shadow-sm">
                                <x-tabler-movie class="size-4" />
                            </span>

                            <div class="min-w-0 grow overflow-hidden rounded-[20px] border border-card-border bg-card-background shadow-md shadow-black/[0.035]">
                                <template x-if="video.url && !video.audio">
                                    <div class="flex min-h-52 items-center justify-center overflow-hidden bg-black/95">
                                        <video
                                            class="{{ $sessionVideoClass }} bg-black object-contain"
                                            :src="video.url"
                                            :poster="video.thumbnail"
                                            controls
                                            preload="metadata"
                                        ></video>
                                    </div>
                                </template>

                                <template x-if="video.url && video.audio">
                                    <div class="flex items-center gap-3 bg-foreground/[0.025] px-4 py-6 sm:px-5">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                            <x-tabler-wave-sine class="size-5" />
                                        </span>
                                        <audio
                                            class="min-w-0 grow"
                                            :src="video.url"
                                            controls
                                            preload="none"
                                        ></audio>
                                    </div>
                                </template>

                                <div class="flex flex-wrap items-center gap-3 border-t border-card-border px-4 py-3.5 sm:px-5">
                                    <div class="min-w-0 grow">
                                        <span class="block truncate text-2xs font-semibold" x-text="video.title"></span>
                                        <span class="mt-0.5 flex flex-wrap items-center gap-1.5 text-[10px] text-label">
                                            <x-tabler-player-play class="size-3" />
                                            <span x-text="video.kind_label"></span>
                                            <template x-if="video.duration">
                                                <span class="contents">
                                                    <span aria-hidden="true">&middot;</span>
                                                    <span x-text="video.duration"></span>
                                                </span>
                                            </template>
                                        </span>
                                    </div>

                                    <template x-if="video.url">
                                        <a
                                            class="inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-button bg-primary px-3.5 text-xs font-medium text-primary-foreground transition-all hover:-translate-y-0.5 hover:bg-primary/90 hover:shadow-xl hover:shadow-primary/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                                            :href="video.url"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            <x-tabler-download class="size-3.5" />
                                            <span x-show="!video.audio">{{ __('Download video') }}</span>
                                            <span x-show="video.audio">{{ __('Download audio') }}</span>
                                        </a>
                                    </template>
                                </div>

                                <p
                                    class="m-0 px-4 pb-3 text-[11px] text-rose-500"
                                    x-cloak
                                    x-show="video.error"
                                    x-text="video.error"
                                ></p>
                            </div>
                        </div>
                    </template>

                    {{-- A failure is something the agent says, so it reads as one. --}}
                    <div
                        class="flex gap-3 sm:gap-4"
                        x-cloak
                        x-show="error"
                    >
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-500 shadow-sm">
                            <x-tabler-alert-triangle class="size-4" />
                        </span>

                        <div class="min-w-0 grow rounded-[20px] border border-rose-500/20 bg-rose-500/5 px-4 py-4 shadow-sm sm:px-5">
                            <p class="m-0 text-2xs font-medium text-rose-500">{{ __('This session could not be finished.') }}</p>

                            <p
                                class="m-0 mt-1 whitespace-pre-line text-[11px] leading-relaxed text-rose-500/85"
                                x-text="error"
                            ></p>

                            <a
                                class="mt-2.5 inline-flex items-center gap-1.5 text-[11px] font-medium text-rose-500 underline-offset-2 hover:underline"
                                href="{{ route('dashboard.user.video-agent.index') }}"
                            >
                                <x-tabler-refresh class="size-3.5" />
                                {{ __('Start a new video') }}
                            </a>
                        </div>
                    </div>

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
                                for="video-agent-reply"
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
                            id="video-agent-reply"
                            rows="2"
                            maxlength="4000"
                            placeholder="{{ __('Make scene two shorter, or ask for a warmer tone.') }}"
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
                            >{{ __('This session is closed.') }}</span>

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
                                <span class="sr-only">{{ __('Send changes') }}</span>
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('video-agent::partials.scripts')
@endsection
