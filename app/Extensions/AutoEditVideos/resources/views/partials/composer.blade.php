@php
    $durationPillOptions = ['' => __('Auto')];

    foreach ($durations as $seconds) {
        $durationPillOptions[(string) $seconds] = $seconds.'s';
    }

    $resolutionPillOptions = collect($resolutions)->mapWithKeys(fn ($value) => [$value => $value])->all();
    $aspectPillOptions = collect($aspectRatios)->mapWithKeys(fn ($value) => [$value => $value])->all();
@endphp

<div
    class="lqd-auto-edit-composer bg-background p-3"
    x-data="autoEditComposer"
    @dragover.prevent="dragOver = true"
    @dragleave.prevent="dragOver = false"
    @drop.prevent="dragOver = false; uploadFiles($event.dataTransfer.files)"
    :class="{ 'ring-2 ring-inset ring-primary': dragOver }"
>
    <form
        method="post"
        action="{{ route('dashboard.user.auto-edit-videos.store') }}"
        @submit="if (submitted || uploading) { $event.preventDefault(); } else { submitted = true; syncSourceIds(); }"
    >
        @csrf

        <template
            x-for="id in sourceIds"
            :key="id"
        >
            <input
                type="hidden"
                name="source_ids[]"
                :value="id"
            />
        </template>

        <textarea
            class="max-h-80 w-full resize-none border-none bg-transparent px-2 py-1.5 text-sm text-foreground placeholder:text-label focus:outline-none focus:ring-0"
            id="auto-edit-prompt"
            name="prompt"
            rows="3"
            maxlength="{{ config('auto-edit-videos.limits.prompt_max', 5000) }}"
            placeholder="{{ __('Cut this interview into a 90 second highlight reel, remove the silences, add captions and background music.') }}"
            required
        >{{ old('prompt') }}</textarea>
        @error('prompt')
            <p class="mt-1.5 px-2 text-2xs text-red-500">{{ $message }}</p>
        @enderror

        {{-- Action row: attach menu, style notes, option pills, submit --}}
        <div class="flex flex-wrap items-center gap-2">
            <x-dropdown.dropdown
                class:dropdown="w-64 p-2"
                trigger-type="click"
                offset-y="10px"
            >
                <x-slot:trigger
                    class="relative size-9 shrink-0 border border-input-border p-0"
                    variant="ghost"
                    title="{{ __('Attach') }}"
                >
                    <x-tabler-plus class="size-4 transition group-[&.lqd-is-active]/dropdown:rotate-45" />
                    <span
                        class="absolute -end-1 -top-1 grid size-5 place-items-center rounded-full bg-primary text-[10px] font-semibold text-primary-foreground"
                        x-cloak
                        x-show="sources.length"
                        x-text="sources.length"
                    ></span>
                    <span class="sr-only">{{ __('Attach') }}</span>
                </x-slot:trigger>

                <x-slot:dropdown>
                    <ul class="m-0 flex list-none flex-col gap-0.5 p-0 [&_button]:w-full [&_button]:items-center [&_button]:gap-2.5 [&_button]:rounded-dropdown [&_button]:px-2.5 [&_button]:py-2 [&_button]:text-2xs [&_button:hover]:bg-foreground/5">
                        <li>
                            <button
                                class="flex"
                                type="button"
                                @click="toggle('collapse'); window.dispatchEvent(new CustomEvent('ae-request-upload'))"
                            >
                                <x-tabler-paperclip class="size-4 shrink-0 opacity-70" />
                                {{ __('Upload files') }}
                            </button>
                        </li>
                        <li>
                            <button
                                class="flex"
                                type="button"
                                @click="toggle('collapse'); window.dispatchEvent(new CustomEvent('ae-open-url'))"
                            >
                                <x-tabler-link class="size-4 shrink-0 opacity-70" />
                                {{ __('Add from URL') }}
                            </button>
                        </li>
                    </ul>
                </x-slot:dropdown>
            </x-dropdown.dropdown>

            <button
                class="relative inline-flex size-9 shrink-0 items-center justify-center rounded-button border border-input-border text-label transition-colors hover:border-primary hover:text-foreground [&.on]:border-primary [&.on]:text-primary"
                type="button"
                :class="{ 'on': styleOpen }"
                @click="styleOpen = !styleOpen"
            >
                <x-tabler-adjustments class="size-4" />
                <span class="sr-only">{{ __('Style notes') }}</span>
            </button>

            <input
                class="hidden"
                type="file"
                x-ref="fileInput"
                accept="video/*,audio/*,image/*"
                multiple
                @change="uploadFiles($event.target.files)"
            />

            <span
                class="text-[11px] text-label"
                x-cloak
                x-show="uploading"
            >{{ __('Uploading…') }}</span>

            <div class="ms-auto flex flex-wrap items-center gap-2">
                @include('auto-edit-videos::partials.option-select', [
                    'name' => 'duration',
                    'icon' => 'tabler-clock',
                    'label' => __('Target length'),
                    'model' => 'duration',
                    'value' => old('duration', ''),
                    'options' => $durationPillOptions,
                ])

                @include('auto-edit-videos::partials.option-select', [
                    'name' => 'target_resolution',
                    'icon' => 'tabler-badge-hd',
                    'label' => __('Resolution'),
                    'model' => 'targetResolution',
                    'value' => old('target_resolution', config('auto-edit-videos.defaults.target_resolution')),
                    'options' => $resolutionPillOptions,
                ])

                @include('auto-edit-videos::partials.option-select', [
                    'name' => 'aspect_ratio',
                    'icon' => 'tabler-aspect-ratio',
                    'label' => __('Aspect ratio'),
                    'model' => 'aspectRatio',
                    'value' => old('aspect_ratio', config('auto-edit-videos.defaults.aspect_ratio')),
                    'options' => $aspectPillOptions,
                ])

                {{-- `:disabled` and not `@disabled(...)`: a Blade directive inside an
                     `<x-*>` tag breaks the component compiler. --}}
                <x-button
                    class="h-9 px-4"
                    type="submit"
                    size="none"
                    :disabled="$isDemo || !$isConfigured"
                    ::disabled="submitted || uploading"
                >
                    <x-tabler-sparkles class="size-4" />
                    <span>{{ __('Start editing') }}</span>
                </x-button>
            </div>
        </div>

        <div
            x-ref="urlRow"
            class="px-2"
            x-cloak
            x-show="showUrl"
            x-collapse
        >
            <div class="mt-2 flex items-center gap-2">
                <x-forms.input
                    class:container="grow"
                    type="url"
                    size="sm"
                    placeholder="{{ __('…or paste a public media URL') }}"
                    x-model="urlInput"
                    @keydown.enter.prevent="addUrl()"
                />
                <x-button
                    class="shrink-0"
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addUrl()"
                    ::disabled="uploading || !urlInput"
                >
                    {{ __('Add') }}
                </x-button>
            </div>
        </div>

        <div
            class="px-2"
            x-cloak
            x-show="styleOpen"
            x-collapse
        >
            <textarea
                class="form-control mt-2"
                id="auto-edit-instructions"
                name="instructions"
                rows="2"
                maxlength="2000"
                placeholder="{{ __('Punchy, upbeat, brand colours, bold captions at the bottom.') }}"
            >{{ old('instructions') }}</textarea>
        </div>

        <p
            class="mt-1.5 px-2 text-2xs text-red-500"
            x-cloak
            x-show="error"
            x-text="error"
        ></p>

        <ul
            class="mt-2 flex flex-col gap-1.5 px-2"
            x-cloak
            x-show="sources.length"
        >
            <template
                x-for="source in sources"
                :key="source.id"
            >
                <li class="bg-foreground/5 flex items-center gap-2 rounded-input px-3 py-2">
                    <x-tabler-file class="text-label size-3.5 shrink-0" />
                    <span
                        class="min-w-0 flex-1 truncate text-2xs"
                        x-text="source.name"
                    ></span>
                    <button
                        class="text-label hover:text-foreground shrink-0 transition"
                        type="button"
                        :aria-label="'{{ __('Remove') }} ' + source.name"
                        @click.stop="removeSource(source)"
                    >
                        <x-tabler-x class="size-3.5" />
                    </button>
                </li>
            </template>
        </ul>

        @error('source_ids')
            <p class="mt-1.5 px-2 text-2xs text-red-500">{{ $message }}</p>
        @enderror

        <p class="text-label mt-2.5 px-3 pb-2 text-center text-4xs leading-relaxed">
            {{ __('You are only charged once a render finishes. Failed edits cost nothing.') }}
        </p>
    </form>
</div>

@push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('autoEditComposer', () => ({
                sources: [],
                sourceIds: [],
                urlInput: '',
                uploading: false,
                dragOver: false,
                submitted: false,
                error: '',
                showUrl: false,
                styleOpen: false,
                duration: {{ Illuminate\Support\Js::from(old('duration', '')) }},
                targetResolution: {{ Illuminate\Support\Js::from(old('target_resolution', config('auto-edit-videos.defaults.target_resolution'))) }},
                aspectRatio: {{ Illuminate\Support\Js::from(old('aspect_ratio', config('auto-edit-videos.defaults.aspect_ratio'))) }},
                maxSources: {{ (int) config('auto-edit-videos.limits.max_sources', 10) }},
                storeUrl: {{ Illuminate\Support\Js::from(route('dashboard.user.auto-edit-videos.sources.store')) }},
                destroyUrlTemplate: {{ Illuminate\Support\Js::from(route('dashboard.user.auto-edit-videos.sources.destroy', ['source' => '__ID__'])) }},

                init() {
                    // The attach menu lives in the dropdown's own Alpine scope,
                    // so it talks to the composer through window events.
                    window.addEventListener('ae-request-upload', () => this.$refs.fileInput && this.$refs.fileInput.click());
                    window.addEventListener('ae-open-url', () => {
                        this.showUrl = true;
                        this.$nextTick(() => this.$refs.urlRow && this.$refs.urlRow.querySelector('input')?.focus());
                    });
                },

                syncSourceIds() {
                    this.sourceIds = this.sources.map((source) => source.id);
                },

                async uploadFiles(fileList) {
                    for (const file of Array.from(fileList)) {
                        if (this.sources.length >= this.maxSources) {
                            this.error = {{ Illuminate\Support\Js::from(__('You have added the maximum number of files.')) }};
                            break;
                        }

                        const body = new FormData();
                        body.append('file', file);
                        await this.send(body);
                    }

                    this.$refs.fileInput.value = '';
                },

                async addUrl() {
                    if (!this.urlInput) {
                        return;
                    }

                    const body = new FormData();
                    body.append('url', this.urlInput);

                    if (await this.send(body)) {
                        this.urlInput = '';
                    }
                },

                async send(body) {
                    this.uploading = true;
                    this.error = '';

                    try {
                        const response = await fetch(this.storeUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Accept': 'application/json',
                            },
                            body,
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.error = data.message ?? '';

                            return false;
                        }

                        this.sources.push(data.source);
                        this.syncSourceIds();

                        return true;
                    } catch (error) {
                        this.error = {{ Illuminate\Support\Js::from(__('Could not reach the server.')) }};

                        return false;
                    } finally {
                        this.uploading = false;
                    }
                },

                async removeSource(source) {
                    this.sources = this.sources.filter((item) => item.id !== source.id);
                    this.syncSourceIds();

                    await fetch(this.destroyUrlTemplate.replace('__ID__', source.id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json',
                        },
                    });
                },
            }));
        });
    </script>
@endpush
