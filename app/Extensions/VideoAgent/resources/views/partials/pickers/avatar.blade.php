{{-- Avatar picker. Opened from the composer's avatar pill via the `va-open` event. --}}
<div
    class="contents"
    x-data="videoAgentPicker({ kind: 'avatar', modalId: 'va-avatar-modal', endpoint: 'avatars', syncEndpoint: 'avatarsSync', filters: { ownership: '', q: '', avatar_type: '' } })"
>
    <x-modal
        class:close-btn="size-7 [&_svg]:size-4"
        class:modal-body="p-0 flex min-h-0 grow flex-col"
        class:modal-container="max-w-none p-0 flex min-h-0 grow flex-col"
        class:modal-content="w-[min(100%,1040px)] h-[min(760px,calc(100vh-3rem))] flex flex-col overflow-hidden"
        class:modal-head="px-6 pt-5 pb-0 border-b-0"
        class:modal-title="text-base font-semibold"
        id="va-avatar-modal"
        disable-focus
        title="{{ __('Choose an avatar') }}"
    >
        <x-slot:modal>
            <div class="flex min-h-0 grow flex-col px-6 pb-6">
                @include('video-agent::partials.demo-guard')

                {{-- Source tabs --}}
                <div class="mb-5 flex flex-wrap items-center gap-1 border-b border-card-border">
                    @foreach ([
                        '' => __('All avatars'),
                        'recent' => __('Recently used'),
                        'private' => __('My avatars'),
                        'public' => __('Stock avatars'),
                    ] as $value => $label)
                        <button
                            class="relative -mb-px border-b-2 border-transparent px-3 pb-3 pt-1 text-2xs font-medium text-label transition hover:text-foreground [&.active]:border-primary [&.active]:text-foreground"
                            type="button"
                            :class="{ 'active': isFilter('ownership', '{{ $value }}') }"
                            @click="setFilter('ownership', '{{ $value }}')"
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

                {{-- Search --}}
                <x-forms.input
                    class="h-10 border-none bg-foreground/5"
                    class:container="mb-4"
                    type="search"
                    placeholder="{{ __('Search avatars') }}"
                    x-model="filters.q"
                    @input.debounce.400ms="reload()"
                />

                {{-- Type chips --}}
                <div class="mb-5 flex flex-wrap gap-2">
                    @foreach ([
                        '' => __('All'),
                        'studio_avatar' => __('Studio'),
                        'digital_twin' => __('Digital twin'),
                        'photo_avatar' => __('Photo'),
                    ] as $value => $label)
                        <button
                            class="rounded-button border border-input-border px-3 py-1.5 text-2xs transition hover:border-primary [&.active]:border-primary [&.active]:bg-primary [&.active]:text-primary-foreground"
                            type="button"
                            @if ($value === 'studio_avatar') x-show="meta.has_studio" x-cloak @endif
                            :class="{ 'active': isFilter('avatar_type', '{{ $value }}') }"
                            @click="setFilter('avatar_type', '{{ $value }}')"
                        >{{ $label }}</button>
                    @endforeach

                    <button
                        class="ms-auto rounded-button px-3 py-1.5 text-2xs text-label underline-offset-2 transition hover:text-foreground hover:underline"
                        type="button"
                        @click="clear()"
                    >{{ __('Let the agent choose') }}</button>
                </div>

                <div
                    class="mb-4 rounded-card border border-card-border bg-card-background p-4"
                    x-show="isFilter('avatar_type', 'digital_twin')"
                    x-cloak
                >
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="m-0 text-2xs font-semibold">{{ __('Create your Digital Twin') }}</p>
                            <p class="m-0 mt-1 text-2xs text-label">{{ __('Upload footage of yourself to create a private avatar.') }}</p>
                        </div>
                        <button
                            class="rounded-button bg-primary px-3 py-2 text-2xs font-medium text-primary-foreground"
                            type="button"
                            @click="showTwinForm = !showTwinForm"
                            x-text="showTwinForm ? messages.cancel : messages.createTwin"
                        ></button>
                    </div>

                    <form
                        class="mt-4 grid gap-3 border-t border-card-border pt-4"
                        x-show="showTwinForm"
                        x-cloak
                        @submit.prevent="uploadTwin()"
                    >
                        <label class="grid gap-1 text-2xs font-medium">
                            {{ __('Avatar name') }}
                            <input
                                class="rounded-button border border-input-border bg-transparent px-3 py-2"
                                type="text"
                                maxlength="191"
                                required
                                x-model="twinName"
                            >
                        </label>
                        <label class="grid gap-1 text-2xs font-medium">
                            {{ __('Training video') }}
                            <input
                                class="rounded-button border border-input-border bg-transparent px-3 py-2"
                                type="file"
                                accept="video/mp4,video/webm"
                                required
                                x-ref="twinFileInput"
                                @change="twinFile = $event.target.files[0]"
                            >
                        </label>
                        <p class="m-0 text-2xs text-label">{{ __('MP4 or WebM, up to 32 MB. Record one person facing the camera with clear speech for 15 seconds to 10 minutes.') }}</p>
                        <p
                            class="m-0 text-2xs text-red-500"
                            x-show="twinError"
                            x-text="twinError"
                        ></p>
                        <button
                            class="w-fit rounded-button bg-primary px-4 py-2 text-2xs font-medium text-primary-foreground disabled:opacity-50"
                            type="submit"
                            :disabled="uploadingTwin || demo"
                            x-text="uploadingTwin ? messages.uploading : messages.createTwin"
                        ></button>
                    </form>
                    <p
                        class="m-0 mt-2 text-2xs text-red-500"
                        x-show="!showTwinForm && twinError"
                        x-text="twinError"
                    ></p>
                </div>

                {{-- Results --}}
                <div class="-mx-1 min-h-0 grow overflow-y-auto px-1">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                        <template
                            x-for="avatar in items"
                            :key="avatar.id"
                        >
                            <div class="overflow-hidden rounded-card border border-card-border bg-card-background">
                                <button
                                    class="group/card w-full text-start transition hover:text-primary disabled:cursor-default disabled:hover:text-foreground"
                                    type="button"
                                    :disabled="!avatar.ready"
                                    @mouseenter="hoverIn($event)"
                                    @mouseleave="hoverOut($event)"
                                    @click="choose(avatar)"
                                >
                                    <div class="relative aspect-[3/4] bg-surface">
                                        <template x-if="avatar.preview_image">
                                            <img
                                                class="size-full object-cover"
                                                :src="avatar.preview_image"
                                                :alt="avatar.name"
                                                loading="lazy"
                                            >
                                        </template>

                                        <template x-if="avatar.preview_video">
                                            <video
                                                class="absolute inset-0 size-full object-cover opacity-0 transition-opacity group-hover/card:opacity-100"
                                                :src="avatar.preview_video"
                                                muted
                                                loop
                                                playsinline
                                                preload="none"
                                            ></video>
                                        </template>

                                        <template x-if="!avatar.preview_image && !avatar.preview_video">
                                            <div class="flex size-full flex-col items-center justify-center gap-2 text-label">
                                                <x-tabler-user class="size-8" />
                                                <span class="text-2xs">{{ __('Preview is being prepared') }}</span>
                                            </div>
                                        </template>

                                        <template x-if="!avatar.ready">
                                            <span
                                                class="absolute top-2 rounded-full bg-orange-500/90 px-2 py-0.5 text-[11px] font-medium text-white end-2"
                                                x-text="avatar.status_label"
                                            ></span>
                                        </template>
                                    </div>

                                    <div class="p-3">
                                        <div
                                            class="truncate text-2xs font-medium"
                                            x-text="avatar.name"
                                        ></div>
                                        <div
                                            class="mt-0.5 truncate text-[11px] text-label"
                                            x-text="[avatar.type_label, avatar.gender].filter(Boolean).join(' · ')"
                                        ></div>
                                    </div>
                                </button>
                                <button
                                    class="w-full border-t border-card-border px-3 py-2 text-start text-2xs font-medium text-primary hover:bg-foreground/5"
                                    type="button"
                                    x-show="avatar.needs_consent"
                                    :disabled="demo"
                                    @click="openConsent(avatar)"
                                >{{ __('Record consent') }}</button>
                            </div>
                        </template>
                    </div>

                    @include('video-agent::partials.pickers.states', ['empty' => __('No avatars match your filters.')])
                </div>
            </div>
        </x-slot:modal>
    </x-modal>
</div>
