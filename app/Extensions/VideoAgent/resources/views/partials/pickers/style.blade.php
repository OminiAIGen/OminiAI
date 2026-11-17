{{-- Look Pack picker: curated visual templates that steer layout, pacing and aesthetic. --}}
<div
    class="contents"
    x-data="videoAgentPicker({ kind: 'style', modalId: 'va-style-modal', endpoint: 'styles', syncEndpoint: 'stylesSync', filters: { tag: '', q: '' } })"
>
    <x-modal
        class:close-btn="size-7 [&_svg]:size-4"
        class:modal-body="p-0 flex min-h-0 grow flex-col"
        class:modal-container="max-w-none p-0 flex min-h-0 grow flex-col"
        class:modal-content="w-[min(100%,1040px)] h-[min(760px,calc(100vh-3rem))] flex flex-col overflow-hidden"
        class:modal-head="px-6 pt-5 pb-0 border-b-0"
        class:modal-title="text-base font-semibold"
        id="va-style-modal"
        disable-focus
        title="{{ __('Choose a Look Pack') }}"
    >
        <x-slot:modal>
            <div class="flex min-h-0 grow flex-col px-6 pb-6">
                @include('video-agent::partials.demo-guard')

                <p class="mb-4 mt-1 text-2xs text-label">
                    {{ __('Look Packs control scene layout, script structure, pacing and aesthetic.') }}
                </p>

                <div class="mb-4 grid gap-3 sm:grid-cols-[1fr_auto]">
                    <x-forms.input
                        class="h-10 border-none bg-foreground/5"
                        type="search"
                        placeholder="{{ __('Search Look Packs') }}"
                        x-model="filters.q"
                        @input.debounce.400ms="reload()"
                    />

                    <button
                        class="inline-flex items-center justify-center gap-1.5 rounded-button border border-input-border px-3 py-1.5 text-2xs text-label transition hover:border-primary hover:text-foreground disabled:opacity-50"
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

                <div class="mb-5 flex flex-wrap items-center gap-2">
                    <button
                        class="rounded-button border border-input-border px-3 py-1.5 text-2xs transition hover:border-primary [&.active]:border-primary [&.active]:bg-primary [&.active]:text-primary-foreground"
                        type="button"
                        :class="{ 'active': isFilter('tag', '') }"
                        @click="setFilter('tag', '')"
                    >{{ __('All') }}</button>

                    <template
                        x-for="tag in (meta.tags || [])"
                        :key="tag.value"
                    >
                        <button
                            class="rounded-button border border-input-border px-3 py-1.5 text-2xs transition hover:border-primary [&.active]:border-primary [&.active]:bg-primary [&.active]:text-primary-foreground"
                            type="button"
                            :class="{ 'active': isFilter('tag', tag.value) }"
                            x-text="tag.label"
                            @click="setFilter('tag', tag.value)"
                        ></button>
                    </template>

                    <button
                        class="ms-auto rounded-button px-3 py-1.5 text-2xs text-label underline-offset-2 transition hover:text-foreground hover:underline"
                        type="button"
                        @click="clear()"
                    >{{ __('Let the agent choose') }}</button>
                </div>

                <div class="-mx-1 min-h-0 grow overflow-y-auto px-1">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <template
                            x-for="style in items"
                            :key="style.id"
                        >
                            <button
                                class="group/card overflow-hidden rounded-card border border-card-border bg-card-background text-start transition hover:border-primary"
                                type="button"
                                @mouseenter="hoverIn($event)"
                                @mouseleave="hoverOut($event)"
                                @click="choose(style)"
                            >
                                <div class="relative aspect-video bg-surface">
                                    <template x-if="style.thumbnail">
                                        <img
                                            class="size-full object-cover"
                                            :src="style.thumbnail"
                                            :alt="style.name"
                                            loading="lazy"
                                        >
                                    </template>

                                    <template x-if="style.preview_video">
                                        <video
                                            class="absolute inset-0 size-full object-cover opacity-0 transition-opacity group-hover/card:opacity-100"
                                            :src="style.preview_video"
                                            muted
                                            loop
                                            playsinline
                                            preload="none"
                                        ></video>
                                    </template>

                                    <template x-if="style.aspect_ratio">
                                        <span
                                            class="absolute bottom-2 rounded-full bg-black/60 px-2 py-0.5 text-[11px] text-white end-2"
                                            x-text="style.aspect_ratio"
                                        ></span>
                                    </template>
                                </div>

                                <div class="p-3">
                                    <div
                                        class="truncate text-2xs font-medium"
                                        x-text="style.name"
                                    ></div>
                                    <div
                                        class="mt-0.5 truncate text-[11px] text-label"
                                        x-text="(style.tags || []).join(' · ')"
                                    ></div>
                                </div>
                            </button>
                        </template>
                    </div>

                    @include('video-agent::partials.pickers.states', ['empty' => __('No Look Packs match your filters.')])
                </div>
            </div>
        </x-slot:modal>
    </x-modal>
</div>
