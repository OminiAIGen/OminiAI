@extends('panel.layout.app')
@section('title', __('Create a video'))
@section('titlebar_subtitle', __('Create directly when you already know the avatar, the script and the shape of the video.'))
@section('titlebar_actions', '')

@php
    $avatarOptions = $avatars->map(fn ($avatar) => [
        'id' => $avatar->heygen_id,
        'name' => $avatar->name ?: $avatar->heygen_id,
        'voice' => $avatar->default_voice_id,
        'preview' => $avatar->preview_image_url,
    ])->values();

    $voiceOptions = $voices->map(fn ($voice) => [
        'id' => $voice->heygen_id,
        'name' => trim(($voice->name ?: $voice->heygen_id) . ' · ' . ($voice->language ?? '')),
    ])->values();
@endphp

@section('content')
    <div
        class="flex flex-col gap-8 py-10"
        x-data="videoAgentCreate()"
    >
        @include('video-agent::partials.tool-header')

        @include('video-agent::partials.catalog-syncing')

        <x-card size="lg">
            <form
                method="post"
                action="{{ route('dashboard.user.video-agent.create.store') }}"
                @submit="submitting = true"
            >
                @csrf
                <input type="hidden" name="type" :value="type">

                {{-- Mode picker --}}
                <div class="mb-6 flex flex-wrap gap-2">
                    @foreach ([
                        'avatar' => __('Avatar'),
                        'image' => __('Image'),
                        'cinematic_avatar' => __('Cinematic'),
                        'studio' => __('Studio scenes'),
                    ] as $value => $label)
                        <button
                            class="rounded-button border border-input-border px-4 py-2 text-2xs transition [&.active]:border-primary [&.active]:bg-primary/10"
                            type="button"
                            :class="{ 'active': type === '{{ $value }}' }"
                            @click="type = '{{ $value }}'"
                        >{{ $label }}</button>
                    @endforeach
                </div>

                {{-- Avatar mode --}}
                <div class="flex flex-col gap-5" x-show="type === 'avatar'">
                    <x-forms.input
                        id="avatar_id"
                        name="avatar_id"
                        type="select"
                        label="{{ __('Avatar look') }}"
                        x-model="avatarId"
                        @change="applyDefaultVoice()"
                    >
                        <option value="">{{ __('Select an avatar') }}</option>
                        @foreach ($avatars as $avatar)
                            <option value="{{ $avatar->heygen_id }}">{{ $avatar->name ?: $avatar->heygen_id }}</option>
                        @endforeach
                    </x-forms.input>

                    <x-forms.input
                        id="script"
                        name="script"
                        type="textarea"
                        rows="5"
                        label="{{ __('Script') }}"
                        placeholder="{{ __('What should the avatar say?') }}"
                    />

                    <div class="grid gap-5 md:grid-cols-2">
                        <x-forms.input
                            id="voice_id"
                            name="voice_id"
                            type="select"
                            label="{{ __('Voice') }}"
                            x-model="voiceId"
                        >
                            <option value="">{{ __('Select a voice') }}</option>
                            @foreach ($voices as $voice)
                                <option value="{{ $voice->heygen_id }}">{{ $voice->name }} · {{ $voice->language }}</option>
                            @endforeach
                        </x-forms.input>

                        <x-forms.input
                            id="engine"
                            name="engine"
                            type="select"
                            label="{{ __('Engine') }}"
                            tooltip="{{ __('Avatar V is the most lifelike, Avatar III is the cheapest per second.') }}"
                        >
                            @foreach (['avatar_iv' => 'Avatar IV', 'avatar_v' => 'Avatar V', 'avatar_iii' => 'Avatar III'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-forms.input>
                    </div>

                    <x-forms.input
                        id="motion_prompt"
                        name="motion_prompt"
                        label="{{ __('Motion prompt') }}"
                        placeholder="{{ __('Gestures naturally, leans in on the key point.') }}"
                    />

                    <x-forms.input
                        id="remove_background"
                        name="remove_background"
                        type="checkbox"
                        switcher
                        value="1"
                        label="{{ __('Remove background') }}"
                    />
                </div>

                {{-- Image mode --}}
                <div class="flex flex-col gap-5" x-show="type === 'image'" x-cloak>
                    <x-forms.input
                        id="image_url"
                        name="image_url"
                        label="{{ __('Image URL') }}"
                        placeholder="https://…"
                    />

                    <x-forms.input
                        id="image_script"
                        name="script"
                        type="textarea"
                        rows="4"
                        label="{{ __('Script') }}"
                    />

                    <x-forms.input
                        id="image_voice_id"
                        name="voice_id"
                        type="select"
                        label="{{ __('Voice') }}"
                    >
                        <option value="">{{ __('Select a voice') }}</option>
                        @foreach ($voices as $voice)
                            <option value="{{ $voice->heygen_id }}">{{ $voice->name }} · {{ $voice->language }}</option>
                        @endforeach
                    </x-forms.input>
                </div>

                {{-- Cinematic mode --}}
                <div class="flex flex-col gap-5" x-show="type === 'cinematic_avatar'" x-cloak>
                    <x-forms.input
                        id="prompt"
                        name="prompt"
                        type="textarea"
                        rows="4"
                        label="{{ __('Prompt') }}"
                        placeholder="{{ __('A slow push-in on the presenter in a sunlit studio.') }}"
                    />

                    <div>
                        <label class="form-label">{{ __('Looks (1-3)') }}</label>
                        <div class="grid gap-2 sm:grid-cols-3">
                            @for ($i = 0; $i < 3; $i++)
                                <select class="form-select" name="avatar_ids[]">
                                    <option value="">{{ __('None') }}</option>
                                    @foreach ($avatars as $avatar)
                                        <option value="{{ $avatar->heygen_id }}">{{ $avatar->name ?: $avatar->heygen_id }}</option>
                                    @endforeach
                                </select>
                            @endfor
                        </div>
                    </div>

                    <x-forms.input
                        id="duration"
                        name="duration"
                        type="number"
                        min="4"
                        max="15"
                        label="{{ __('Duration (seconds)') }}"
                        tooltip="{{ __('Cinematic avatar renders are billed at a flat rate per video.') }}"
                    />
                </div>

                {{-- Studio mode --}}
                <div class="flex flex-col gap-5" x-show="type === 'studio'" x-cloak>
                    <template x-for="(scene, index) in scenes" :key="index">
                        <div class="rounded-card border border-card-border p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-2xs font-medium text-label" x-text="`{{ __('Scene') }} ${index + 1}`"></span>
                                <button
                                    class="text-2xs text-red-500"
                                    type="button"
                                    @click="scenes.splice(index, 1)"
                                    x-show="scenes.length > 1"
                                >{{ __('Remove') }}</button>
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                <select class="form-select" :name="`scenes[${index}][avatar_id]`">
                                    <option value="">{{ __('Avatar') }}</option>
                                    @foreach ($avatars as $avatar)
                                        <option value="{{ $avatar->heygen_id }}">{{ $avatar->name ?: $avatar->heygen_id }}</option>
                                    @endforeach
                                </select>

                                <select class="form-select" :name="`scenes[${index}][voice_id]`">
                                    <option value="">{{ __('Voice') }}</option>
                                    @foreach ($voices as $voice)
                                        <option value="{{ $voice->heygen_id }}">{{ $voice->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <textarea
                                class="form-input mt-3 w-full"
                                rows="3"
                                :name="`scenes[${index}][script]`"
                                placeholder="{{ __('Scene script') }}"
                            ></textarea>
                        </div>
                    </template>

                    <x-button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="scenes.push({})"
                        ::disabled="scenes.length >= 50"
                    >
                        <x-tabler-plus class="size-4" />
                        {{ __('Add scene') }}
                    </x-button>
                </div>

                {{-- Shared output options --}}
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <x-forms.input
                        id="resolution"
                        name="resolution"
                        type="select"
                        label="{{ __('Resolution') }}"
                    >
                        @foreach (['720p', '1080p', '4k'] as $value)
                            <option
                                value="{{ $value }}"
                                @selected(setting('video_agent_default_resolution', '1080p') === $value)
                            >{{ strtoupper($value) }}</option>
                        @endforeach
                    </x-forms.input>

                    <x-forms.input
                        id="aspect_ratio"
                        name="aspect_ratio"
                        type="select"
                        label="{{ __('Aspect ratio') }}"
                    >
                        @foreach (['16:9', '9:16', '4:5', '5:4', '1:1', 'auto'] as $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </x-forms.input>
                </div>

                <x-button
                    class="mt-6 w-full"
                    type="submit"
                    ::disabled="submitting"
                >
                    {{ __('Render video') }}
                </x-button>
            </form>
        </x-card>
    </div>

    @push('script')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('videoAgentCreate', () => ({
                    type: 'avatar',
                    avatarId: '',
                    voiceId: '',
                    scenes: [{}],
                    submitting: false,
                    avatars: {{ Js::from($avatarOptions) }},

                    applyDefaultVoice() {
                        const avatar = this.avatars.find((item) => item.id === this.avatarId);

                        if (avatar?.voice && !this.voiceId) {
                            this.voiceId = avatar.voice;
                        }
                    },
                }));
            });
        </script>
    @endpush
@endsection
