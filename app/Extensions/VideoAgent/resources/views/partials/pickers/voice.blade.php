{{-- Voice picker. Rows rather than cards, because the useful signal is the audio. --}}
<div
    class="contents"
    x-data="videoAgentPicker({ kind: 'voice', modalId: 'va-voice-modal', endpoint: 'voices', syncEndpoint: 'voicesSync', pageSize: 30, filters: { type: '', q: '', language: '', gender: '' } })"
>
    <x-modal
        class:close-btn="size-7 [&_svg]:size-4"
        class:modal-body="p-0 flex min-h-0 grow flex-col"
        class:modal-container="max-w-none p-0 flex min-h-0 grow flex-col"
        class:modal-content="w-[min(100%,880px)] h-[min(760px,calc(100vh-3rem))] flex flex-col overflow-hidden"
        class:modal-head="px-6 pt-5 pb-0 border-b-0"
        class:modal-title="text-base font-semibold"
        id="va-voice-modal"
        disable-focus
        title="{{ __('Choose a voice') }}"
    >
        <x-slot:modal>
            <div class="flex min-h-0 grow flex-col px-6 pb-6">
                @include('video-agent::partials.demo-guard')

                <div class="mb-5 flex flex-wrap items-center gap-1 border-b border-card-border">
                    @foreach ([
                        '' => __('All voices'),
                        'recent' => __('Recently used'),
                        'private' => __('My voices'),
                        'public' => __('Stock voices'),
                    ] as $value => $label)
                        <button
                            class="relative -mb-px border-b-2 border-transparent px-3 pb-3 pt-1 text-2xs font-medium text-label transition hover:text-foreground [&.active]:border-primary [&.active]:text-foreground"
                            type="button"
                            :class="{ 'active': isFilter('type', '{{ $value }}') }"
                            @click="setFilter('type', '{{ $value }}')"
                        >{{ $label }}</button>
                    @endforeach

                    <button
                        class="ms-auto mb-2 inline-flex items-center gap-1.5 rounded-button px-3 py-1.5 text-2xs text-label transition hover:bg-foreground/5 hover:text-foreground disabled:opacity-50"
                        type="button"
                        ::disabled="refreshing || demo"
                        @click="refresh()"
                    >
                        <x-tabler-refresh
                            class="size-3.5"
                            ::class="refreshing && 'animate-spin'"
                        />
                        {{ __('Refresh catalog') }}
                    </button>
                </div>

                <div class="mb-5 grid gap-3 sm:grid-cols-[1fr_auto_auto]">
                    <x-forms.input
                        class="h-10 border-none bg-foreground/5"
                        type="search"
                        placeholder="{{ __('Search voices') }}"
                        x-model="filters.q"
                        @input.debounce.400ms="reload()"
                    />

                    <span class="relative inline-flex items-center sm:w-44">
                        <x-forms.input
                            class="h-10 w-full bg-none pe-8 ps-3"
                            class:container="w-full"
                            type="select"
                            x-model="filters.language"
                            @change="reload()"
                        >
                            <option value="">{{ __('All languages') }}</option>
                            <template
                                x-for="language in (meta.languages || [])"
                                :key="language"
                            >
                                <option
                                    :value="language"
                                    x-text="language"
                                ></option>
                            </template>
                        </x-forms.input>

                        <x-tabler-chevron-down class="pointer-events-none absolute size-3.5 text-label end-2.5" />
                    </span>

                    <span class="relative inline-flex items-center sm:w-36">
                        <x-forms.input
                            class="h-10 w-full bg-none pe-8 ps-3"
                            class:container="w-full"
                            type="select"
                            x-model="filters.gender"
                            @change="reload()"
                        >
                            <option value="">{{ __('Any gender') }}</option>
                            <option value="male">{{ __('Male') }}</option>
                            <option value="female">{{ __('Female') }}</option>
                        </x-forms.input>

                        <x-tabler-chevron-down class="pointer-events-none absolute size-3.5 text-label end-2.5" />
                    </span>
                </div>

                <div class="-mx-1 min-h-0 grow overflow-y-auto px-1">
                    <ul class="m-0 flex list-none flex-col gap-1 p-0">
                        <template
                            x-for="voice in items"
                            :key="voice.id"
                        >
                            <li class="flex items-center gap-3 rounded-card px-3 py-2.5 transition hover:bg-foreground/5">
                                <button
                                    class="inline-flex size-9 shrink-0 items-center justify-center rounded-full border border-input-border transition hover:border-primary hover:text-primary disabled:opacity-30"
                                    type="button"
                                    ::disabled="!voice.preview"
                                    @click.stop="togglePreview(voice)"
                                >
                                    <x-tabler-player-play
                                        class="size-4 fill-current"
                                        x-show="playingId !== voice.id"
                                    />
                                    <x-tabler-player-pause
                                        class="size-4 fill-current"
                                        x-cloak
                                        x-show="playingId === voice.id"
                                    />
                                </button>

                                <button
                                    class="flex min-w-0 grow flex-col text-start"
                                    type="button"
                                    @click="choose(voice)"
                                >
                                    <span
                                        class="truncate text-2xs font-medium"
                                        x-text="voice.name"
                                    ></span>
                                    <span
                                        class="truncate text-[11px] text-label"
                                        x-text="[voice.language, voice.gender].filter(Boolean).join(' · ')"
                                    ></span>
                                </button>

                                <x-badge
                                    class="shrink-0"
                                    ::class="voice.type === 'public' ? 'badge-default' : 'badge-success'"
                                >
                                    <span x-text="voice.type === 'public' ? '{{ __('Stock') }}' : '{{ __('Mine') }}'"></span>
                                </x-badge>
                            </li>
                        </template>
                    </ul>

                    @include('video-agent::partials.pickers.states', ['empty' => __('No voices match your filters.')])
                </div>

                <div class="mt-4 flex justify-end border-t border-card-border pt-4">
                    <button
                        class="text-2xs text-label underline-offset-2 transition hover:text-foreground hover:underline"
                        type="button"
                        @click="clear()"
                    >{{ __('Let the agent choose') }}</button>
                </div>

                <audio
                    class="hidden"
                    x-ref="preview"
                    @ended="playingId = null"
                ></audio>
            </div>
        </x-slot:modal>
    </x-modal>
</div>
