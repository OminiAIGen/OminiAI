{{-- Standalone My Videos grid: Today / Recent Videos sections in the studio card style. --}}
<div x-data="autoEditStudio({{ Js::from(['today' => $todayEntries ?? [], 'previous' => $previousEntries ?? []]) }})">
    <div x-show="todayEntries.length === 0 && previousEntries.length === 0">
        <x-empty-state
            icon="tabler-scissors"
            :title="__('No edits yet')"
            :description="__('Add your footage and describe the video you want.')"
        />
    </div>

    <div
        class="flex flex-col gap-5"
        x-show="todayEntries.length > 0 || previousEntries.length > 0"
        x-cloak
    >
        <section x-show="todayEntries.length > 0">
            <div class="mb-5 flex items-center justify-between border-b py-2.5">
                <p class="mb-0 text-[12px] font-semibold">
                    {{ __('Today') }}
                </p>
                <x-button
                    class="text-[12px] font-semibold text-primary hover:underline"
                    variant="link"
                    size="none"
                    type="button"
                    @click.prevent="toggleSelectAll('today')"
                >
                    <span x-text="hasSelectedToday ? '{{ __('Deselect All') }}' : '{{ __('Select All') }}'"></span>
                </x-button>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <template
                    x-for="entry in todayEntries"
                    :key="entryKey(entry)"
                >
                    @include('auto-edit-videos::partials.my-videos-card')
                </template>
            </div>
        </section>

        <section x-show="previousEntries.length > 0">
            <div class="mb-5 flex items-center justify-between border-b py-2.5">
                <p class="mb-0 text-[12px] font-semibold">
                    {{ __('Recent Videos') }}
                </p>
                <x-button
                    class="text-[12px] font-semibold text-primary hover:underline"
                    variant="link"
                    size="none"
                    type="button"
                    @click.prevent="toggleSelectAll('previous')"
                >
                    <span x-text="hasSelectedPrevious ? '{{ __('Deselect All') }}' : '{{ __('Select All') }}'"></span>
                </x-button>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <template
                    x-for="entry in previousEntries"
                    :key="entryKey(entry)"
                >
                    @include('auto-edit-videos::partials.my-videos-card')
                </template>
            </div>
        </section>
    </div>

    @include('auto-edit-videos::partials.my-videos-modal')

    <div
        class="pointer-events-none fixed bottom-8 end-0 start-0 z-20 transition-all max-lg:bottom-[calc(var(--bottom-menu-height)+1rem)] lg:start-[--navbar-width]"
        x-show="totalSelected > 0"
        x-cloak
        x-transition.scale-95
    >
        <div class="container">
            <form
                class="pointer-events-auto flex flex-col items-center justify-between gap-1 rounded-full border border-foreground/5 bg-background px-6 py-4 shadow-xl shadow-black/5 md:flex-row md:py-1 md:pe-1"
                x-data="{ selectedAction: 'delete' }"
                @submit.prevent="bulkDelete"
            >
                <span
                    class="text-2xs font-medium"
                    x-text="totalSelected + ' {{ __('selected') }}'"
                ></span>

                <div class="flex items-center gap-2">
                    <x-forms.input
                        class="w-full rounded-full pe-12 md:w-auto md:pe-12"
                        type="select"
                        size="md"
                        x-model="selectedAction"
                    >
                        <option value="delete">
                            {{ __('Move to Trash') }}
                        </option>
                    </x-forms.input>

                    <x-button
                        type="submit"
                        ::disabled="deleting"
                    >
                        <span x-text="deleting ? '{{ __('Deleting...') }}' : '{{ __('Apply') }}'"></span>
                    </x-button>

                    <x-button
                        variant="outline"
                        hover-variant="none"
                        type="button"
                        @click="cancelSelection"
                    >
                        {{ __('Cancel') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>

@pushOnce('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('autoEditStudio', (initial = { today: [], previous: [] }) => ({
                todayEntries: Array.isArray(initial.today) ? initial.today : [],
                previousEntries: Array.isArray(initial.previous) ? initial.previous : [],
                selectedKeys: [],
                modalOpen: false,
                modalEntry: null,
                deleting: false,
                _timer: null,

                init() {
                    this.scheduleTick();
                },

                destroy() {
                    if (this._timer) {
                        clearTimeout(this._timer);
                    }
                },

                entryKey(entry) {
                    return `auto-edit-${entry.id}`;
                },

                allEntries() {
                    return [...this.todayEntries, ...this.previousEntries];
                },

                isSelected(entry) {
                    return this.selectedKeys.includes(this.entryKey(entry));
                },

                toggleEntrySelection(entry) {
                    const key = this.entryKey(entry);

                    if (this.selectedKeys.includes(key)) {
                        this.selectedKeys = this.selectedKeys.filter((k) => k !== key);
                    } else {
                        this.selectedKeys.push(key);
                    }
                },

                bucketEntries(bucket) {
                    return bucket === 'today' ? this.todayEntries : this.previousEntries;
                },

                get totalSelected() {
                    return this.selectedKeys.length;
                },

                get hasSelectedToday() {
                    if (this.todayEntries.length === 0) {
                        return false;
                    }

                    return this.todayEntries.every((e) => this.selectedKeys.includes(this.entryKey(e)));
                },

                get hasSelectedPrevious() {
                    if (this.previousEntries.length === 0) {
                        return false;
                    }

                    return this.previousEntries.every((e) => this.selectedKeys.includes(this.entryKey(e)));
                },

                toggleSelectAll(bucket) {
                    const entries = this.bucketEntries(bucket);
                    const keys = entries.map((e) => this.entryKey(e));
                    const all = bucket === 'today' ? this.hasSelectedToday : this.hasSelectedPrevious;

                    if (all) {
                        this.selectedKeys = this.selectedKeys.filter((k) => !keys.includes(k));
                    } else {
                        const merged = new Set(this.selectedKeys);
                        keys.forEach((k) => merged.add(k));
                        this.selectedKeys = Array.from(merged);
                    }
                },

                cancelSelection() {
                    this.selectedKeys = [];
                },

                async bulkDelete() {
                    if (this.deleting || this.selectedKeys.length === 0) {
                        return;
                    }

                    if (!window.confirm('{{ __('Delete the selected edits and their sessions? This action can\'t be undone.') }}')) {
                        return;
                    }

                    this.deleting = true;

                    try {
                        const uuids = [...new Set(this.allEntries()
                            .filter((e) => this.selectedKeys.includes(this.entryKey(e)))
                            .map((e) => e.session_uuid)
                            .filter(Boolean))];

                        const res = await fetch('{{ route('dashboard.user.auto-edit-videos.bulk-delete') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                            body: JSON.stringify({ uuids }),
                        });

                        if (!res.ok) {
                            window.toastr?.error('{{ __('Could not delete the edits.') }}');

                            return;
                        }

                        const droppedSessions = new Set(uuids);
                        const drop = (rows) => rows.filter((e) => !droppedSessions.has(e.session_uuid));
                        this.todayEntries = drop(this.todayEntries);
                        this.previousEntries = drop(this.previousEntries);
                        this.selectedKeys = [];
                    } catch (_) {
                        window.toastr?.error('{{ __('Network error.') }}');
                    } finally {
                        this.deleting = false;
                    }
                },

                openVideoModal(entry) {
                    if (!entry || entry.status !== 'completed' || !entry.video_url) {
                        return;
                    }

                    this.modalEntry = entry;
                    this.modalOpen = true;
                    document.documentElement.classList.add('overflow-hidden');
                },

                closeVideoModal() {
                    this.modalOpen = false;
                    document.documentElement.classList.remove('overflow-hidden');
                    this.$nextTick(() => {
                        const videoEl = this.$refs.modalVideo;

                        if (videoEl) {
                            videoEl.pause();
                            videoEl.removeAttribute('src');
                        }

                        this.modalEntry = null;
                    });
                },

                downloadFilename(entry) {
                    const title = (entry.title || 'video').toString().trim();
                    const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 80) || 'video';
                    let ext = 'mp4';

                    try {
                        const path = new URL(entry.video_url || '', window.location.origin).pathname;
                        const match = path.match(/\.([a-z0-9]{2,5})$/i);

                        if (match) {
                            ext = match[1];
                        }
                    } catch (_) {
                        /* fall through */
                    }

                    return `${slug}.${ext}`;
                },

                isTerminal(entry) {
                    return entry.status === 'completed' || entry.status === 'failed' || entry.status === 'canceled';
                },

                hasPending() {
                    return this.allEntries().some((e) => !this.isTerminal(e));
                },

                scheduleTick() {
                    if (this._timer) {
                        clearTimeout(this._timer);
                    }

                    if (!this.hasPending()) {
                        return;
                    }

                    this._timer = setTimeout(() => this.pollPending(), 4000);
                },

                async pollPending() {
                    const pending = this.allEntries().filter((e) => !this.isTerminal(e));

                    await Promise.all(pending.map((e) => this.refreshEntry(e)));
                    this.scheduleTick();
                },

                async refreshEntry(entry) {
                    if (!entry.status_url) {
                        return;
                    }

                    try {
                        const res = await fetch(entry.status_url, { headers: { Accept: 'application/json' } });

                        if (!res.ok) {
                            return;
                        }

                        const json = await res.json();
                        const list = Array.isArray(json.entries) ? json.entries : [];
                        const fresh = list.find((item) => item.id === entry.id);

                        if (!fresh) {
                            return;
                        }

                        const patch = (rows) => rows.map((e) => this.entryKey(e) === this.entryKey(entry) ? { ...e, ...fresh } : e);
                        this.todayEntries = patch(this.todayEntries);
                        this.previousEntries = patch(this.previousEntries);
                    } catch (_) {
                        /* soft fail */
                    }
                },
            }));
        });
    </script>
@endPushOnce
