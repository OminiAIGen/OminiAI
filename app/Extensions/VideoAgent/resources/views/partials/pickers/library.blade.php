{{--
    Everything the agent has rendered. Nested x-data adds selection and bulk
    delete on top of the shared picker scope, which finally gives the
    `videos.bulk-delete` route a UI.
--}}
<div
    class="contents"
    x-data="videoAgentPicker({ kind: 'library', modalId: 'va-library-modal', endpoint: 'library', pageSize: 12, filters: { q: '', kind: '', status: '' } })"
>
    <x-modal
        class:close-btn="size-7 [&_svg]:size-4"
        class:modal-body="p-0 flex min-h-0 grow flex-col"
        class:modal-container="max-w-none p-0 flex min-h-0 grow flex-col"
        class:modal-content="w-[min(100%,1100px)] h-[min(780px,calc(100vh-3rem))] flex flex-col overflow-hidden"
        class:modal-head="px-6 pt-5 pb-0 border-b-0"
        class:modal-title="text-base font-semibold"
        id="va-library-modal"
        disable-focus
        title="{{ __('Library') }}"
    >
        <x-slot:modal>
            <div
                class="flex min-h-0 grow flex-col px-6 pb-6"
                x-data="{
                    selected: [],
                    deleting: false,
                    toggle(uuid) {
                        this.selected = this.selected.includes(uuid)
                            ? this.selected.filter((item) => item !== uuid)
                            : [...this.selected, uuid];
                    },
                    async bulkDelete() {
                        if (!this.selected.length || !confirm('{{ __('Delete the selected videos?') }}')) return;
                        this.deleting = true;
                        try {
                            await fetch('{{ route('dashboard.user.video-agent.videos.bulk-delete') }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({ uuids: this.selected }),
                            });
                            this.selected = [];
                            await this.reload();
                        } finally {
                            this.deleting = false;
                        }
                    },
                }"
            >
                <div class="mb-5 grid gap-3 sm:grid-cols-[1fr_auto_auto]">
                    <x-forms.input
                        class="h-10 border-none bg-foreground/5"
                        type="search"
                        placeholder="{{ __('Search your videos') }}"
                        x-model="filters.q"
                        @input.debounce.400ms="reload()"
                    />

                    <span class="relative inline-flex items-center sm:w-44">
                        <x-forms.input
                            class="h-10 w-full bg-none pe-8 ps-3"
                            class:container="w-full"
                            type="select"
                            x-model="filters.kind"
                            @change="reload()"
                        >
                            <option value="">{{ __('All types') }}</option>
                            <template
                                x-for="option in (meta.kinds || [])"
                                :key="option.value"
                            >
                                <option
                                    :value="option.value"
                                    x-text="option.label"
                                ></option>
                            </template>
                        </x-forms.input>

                        <x-tabler-chevron-down class="pointer-events-none absolute size-3.5 text-label end-2.5" />
                    </span>

                    <span class="relative inline-flex items-center sm:w-40">
                        <x-forms.input
                            class="h-10 w-full bg-none pe-8 ps-3"
                            class:container="w-full"
                            type="select"
                            x-model="filters.status"
                            @change="reload()"
                        >
                            <option value="">{{ __('All statuses') }}</option>
                            <template
                                x-for="option in (meta.statuses || [])"
                                :key="option.value"
                            >
                                <option
                                    :value="option.value"
                                    x-text="option.label"
                                ></option>
                            </template>
                        </x-forms.input>

                        <x-tabler-chevron-down class="pointer-events-none absolute size-3.5 text-label end-2.5" />
                    </span>
                </div>

                <div
                    class="mb-4 flex items-center justify-between gap-3 rounded-card bg-primary/10 px-4 py-2.5"
                    x-cloak
                    x-show="selected.length"
                >
                    <span
                        class="text-2xs font-medium"
                        x-text="`${selected.length} {{ __('selected') }}`"
                    ></span>

                    <div class="flex items-center gap-2">
                        <button
                            class="text-2xs text-label transition hover:text-foreground"
                            type="button"
                            @click="selected = []"
                        >{{ __('Cancel') }}</button>

                        <x-button
                            type="button"
                            variant="outline"
                            size="sm"
                            ::disabled="deleting"
                            @click="bulkDelete()"
                        >
                            <x-tabler-trash class="size-4" />
                            {{ __('Delete') }}
                        </x-button>
                    </div>
                </div>

                <div class="-mx-1 min-h-0 grow overflow-y-auto px-1">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <template
                            x-for="video in items"
                            :key="video.uuid"
                        >
                            <div
                                class="flex flex-col overflow-hidden rounded-card border border-card-border bg-card-background transition [&.picked]:border-primary"
                                :class="{ 'picked': selected.includes(video.uuid) }"
                            >
                                <div class="relative aspect-video bg-surface">
                                    <template x-if="video.url && !video.audio">
                                        <video
                                            class="size-full bg-black object-contain"
                                            :src="video.url"
                                            :poster="video.thumbnail"
                                            controls
                                            preload="metadata"
                                        ></video>
                                    </template>

                                    <template x-if="video.url && video.audio">
                                        <div class="flex size-full items-center justify-center p-4">
                                            <audio
                                                class="w-full"
                                                :src="video.url"
                                                controls
                                                preload="none"
                                            ></audio>
                                        </div>
                                    </template>

                                    <template x-if="!video.url">
                                        <div
                                            class="flex size-full items-center justify-center text-2xs text-label"
                                            x-text="video.label"
                                        ></div>
                                    </template>

                                    <label class="absolute top-2 inline-flex cursor-pointer items-center start-2">
                                        <input
                                            class="size-4 cursor-pointer rounded border-input-border accent-primary"
                                            type="checkbox"
                                            :checked="selected.includes(video.uuid)"
                                            @change="toggle(video.uuid)"
                                        >
                                        <span class="sr-only">{{ __('Select') }}</span>
                                    </label>
                                </div>

                                <div class="flex grow flex-col gap-1 p-3">
                                    <div
                                        class="truncate text-2xs font-medium"
                                        x-text="video.title"
                                    ></div>
                                    <div
                                        class="truncate text-[11px] text-label"
                                        x-text="[video.kind_label, video.duration, video.at].filter(Boolean).join(' · ')"
                                    ></div>

                                    <p
                                        class="m-0 text-[11px] text-rose-500"
                                        x-cloak
                                        x-show="video.error"
                                        x-text="video.error"
                                    ></p>

                                    <div class="mt-auto flex flex-wrap items-center gap-2 pt-2">
                                        <template x-if="video.url">
                                            <a
                                                class="text-[11px] text-primary underline-offset-2 hover:underline"
                                                :href="video.url"
                                                target="_blank"
                                                rel="noopener"
                                            >{{ __('Download') }}</a>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    @include('video-agent::partials.pickers.states', ['empty' => __('Nothing rendered yet.')])
                </div>
            </div>
        </x-slot:modal>
    </x-modal>
</div>
