{{--
    The prompt composer. Selection pills open picker modals and mirror their
    choice into hidden inputs, so this stays a plain form POST to
    `video-agent.store` — the same fields StoreVideoAgentSessionRequest already
    validates.
--}}
@php
    $selections = \App\Extensions\VideoAgent\System\Http\Controllers\VideoAgentController::pillSelections();

    $videoAgentPills = [
        ['kind' => 'avatar', 'icon' => 'tabler-user-circle', 'label' => __('Avatar')],
        ['kind' => 'voice', 'icon' => 'tabler-microphone', 'label' => __('Voice')],
        ['kind' => 'style', 'icon' => 'tabler-palette', 'label' => __('Look Pack')],
        ['kind' => 'brand', 'icon' => 'tabler-color-swatch', 'label' => __('Brand')],
    ];

    $durationOptions = ['' => __('Auto')];

    foreach ($durations as $seconds) {
        $durationOptions[$seconds] = $seconds < 60
            ? __(':seconds sec', ['seconds' => $seconds])
            : __(':minutes min', ['minutes' => intdiv($seconds, 60)]);
    }

    $modeOptions = collect($modes)
        ->mapWithKeys(fn ($sessionMode) => [$sessionMode->value => $sessionMode->label()])
        ->all();
@endphp

<form
    class="lqd-va-composer"
    method="post"
    action="{{ route('dashboard.user.video-agent.store') }}"
    x-data="videoAgentComposer({{ Js::from([
        'prompt' => old('prompt', ''),
        'mode' => old('mode', $defaultMode),
        'orientation' => old('orientation', $defaultOrientation),
        'duration' => old('duration', ''),
        'instructions' => old('instructions', ''),
        'selection' => $selections,
    ]) }})"
    @submit="submit($event)"
>
    @csrf

    <div class="flex flex-col gap-3 bg-background p-3">
        {{-- Selection pills --}}
        <div class="flex flex-wrap gap-2">
                @foreach ($videoAgentPills as $pill)
                    <button
                        class="group/pill flex items-center gap-2 rounded-full border border-input-border bg-background py-1.5 pe-2.5 ps-1.5 text-2xs transition hover:border-primary [&.picked]:border-primary [&.picked]:bg-primary/5"
                        type="button"
                        :class="{ 'picked': selection.{{ $pill['kind'] }} }"
                        @click="openPicker('{{ $pill['kind'] }}')"
                    >
                        <span class="flex size-7 shrink-0 items-center justify-center overflow-hidden rounded-full bg-foreground/5">
                            <template x-if="selection.{{ $pill['kind'] }} && selection.{{ $pill['kind'] }}.thumbnail">
                                <img
                                    class="size-full object-cover"
                                    :src="selection.{{ $pill['kind'] }}.thumbnail"
                                    alt=""
                                >
                            </template>

                            <template x-if="!(selection.{{ $pill['kind'] }} && selection.{{ $pill['kind'] }}.thumbnail)">
                                <span class="contents">
                                    <x-dynamic-component
                                        class="size-4 text-label"
                                        :component="$pill['icon']"
                                    />
                                </span>
                            </template>
                        </span>

                        <span class="flex max-w-[9rem] flex-col items-start leading-tight">
                            <span
                                class="w-full truncate text-start font-medium"
                                x-text="pillLabel('{{ $pill['kind'] }}', '{{ __('Auto') }}')"
                            >{{ $selections[$pill['kind']]['name'] ?? __('Auto') }}</span>
                            <span class="text-[10px] text-label">{{ $pill['label'] }}</span>
                        </span>

                        <x-tabler-chevron-down class="size-3.5 shrink-0 text-label" />
                    </button>
                @endforeach
        </div>

        {{-- Prompt --}}
        <textarea
            class="max-h-80 w-full resize-none border-none bg-transparent px-2 py-1.5 text-sm text-foreground placeholder:text-label focus:outline-none focus:ring-0"
            name="prompt"
            rows="3"
            maxlength="{{ config('video-agent.limits.prompt_max') }}"
            placeholder="{{ __('Ask for a video, an avatar, or anything in between — I can get you started.') }}"
            x-ref="prompt"
            x-model="prompt"
            @input="autogrow()"
            @keydown.enter="if (! $event.shiftKey) { $event.preventDefault(); $el.form.requestSubmit(); }"
        >{{ old('prompt', '') }}</textarea>

        {{-- Attachments --}}
        <div
            class="flex flex-wrap gap-2 px-2"
            x-cloak
            x-show="assets.length"
        >
            <template
                x-for="asset in assets"
                :key="asset.asset_id"
            >
                <span class="inline-flex max-w-52 items-center gap-1.5 rounded-full bg-surface px-2.5 py-1 text-[11px]">
                    <x-tabler-paperclip class="size-3 shrink-0 opacity-60" />
                    <span
                        class="truncate"
                        x-text="asset.name"
                    ></span>
                    <button
                        class="shrink-0 opacity-60 transition hover:opacity-100"
                        type="button"
                        @click="removeAsset(asset)"
                    >
                        <x-tabler-x class="size-3" />
                        <span class="sr-only">{{ __('Remove') }}</span>
                    </button>
                    <input
                        type="hidden"
                        name="asset_ids[]"
                        :value="asset.asset_id"
                    >
                </span>
            </template>
        </div>

        {{--
            Custom instructions. HeyGen has no parameter for these — its guide
            calls it "a style paragraph" and says to put it in the prompt — so
            this is appended server-side rather than sent as a field.
        --}}
        <div
            class="px-2"
            x-cloak
            x-show="instructionsOpen"
            x-collapse
        >
            <textarea
                class="max-h-40 w-full resize-none rounded-card border border-input-border bg-background px-3 py-2 text-2xs text-foreground placeholder:text-label focus:border-primary focus:outline-none focus:ring-0"
                name="instructions"
                rows="2"
                maxlength="{{ \App\Extensions\VideoAgent\System\Http\Requests\StoreVideoAgentSessionRequest::INSTRUCTIONS_MAX }}"
                placeholder="{{ __('Tone, pacing, wording to avoid — anything the agent should always follow.') }}"
                x-ref="instructions"
                x-model="instructions"
            >{{ old('instructions', '') }}</textarea>
        </div>

        {{-- Action row --}}
        <div class="flex flex-wrap items-center gap-2">
            @include('video-agent::partials.tools-menu')

            <button
                class="relative inline-flex size-9 shrink-0 items-center justify-center rounded-button border border-input-border text-label transition-colors hover:border-primary hover:text-foreground [&.on]:border-primary [&.on]:text-primary"
                type="button"
                :class="{ 'on': instructionsOpen || instructions.trim() }"
                @click="toggleInstructions()"
            >
                <x-tabler-adjustments class="size-4" />

                {{-- A collapsed instruction must not look like no instruction. --}}
                <span
                    class="absolute top-1 size-1.5 rounded-full bg-primary end-1"
                    x-cloak
                    x-show="!instructionsOpen && instructions.trim()"
                ></span>

                <span class="sr-only">{{ __('Custom instructions') }}</span>
            </button>

            <input
                class="hidden"
                type="file"
                x-ref="file"
                @change="uploadFiles($event)"
                multiple
            >

            <span
                class="text-[11px] text-label"
                x-cloak
                x-show="uploading"
            >{{ __('Uploading…') }}</span>

            <div class="ms-auto flex flex-wrap items-center gap-2">
                @include('video-agent::partials.option-select', [
                    'name' => 'duration',
                    'icon' => 'tabler-clock',
                    'label' => __('Target length'),
                    'model' => 'duration',
                    'value' => old('duration', ''),
                    'options' => $durationOptions,
                ])

                {{-- HeyGen takes landscape or portrait; Auto means omit the field. --}}
                @include('video-agent::partials.option-select', [
                    'name' => 'orientation',
                    'icon' => 'tabler-aspect-ratio',
                    'label' => __('Orientation'),
                    'model' => 'orientation',
                    'value' => old('orientation', $defaultOrientation),
                    'options' => ['' => __('Auto'), 'portrait' => __('Portrait'), 'landscape' => __('Landscape')],
                ])

                @include('video-agent::partials.option-select', [
                    'name' => 'mode',
                    'icon' => 'tabler-route',
                    'label' => __('Mode'),
                    'model' => 'mode',
                    'value' => old('mode', $defaultMode),
                    'options' => $modeOptions,
                ])

                <x-button
                    class="h-9 px-4"
                    type="submit"
                    size="none"
                    ::disabled="!canSubmit"
                >
                    <x-tabler-loader-2
                        class="size-4 animate-spin"
                        x-cloak
                        x-show="submitting"
                    />
                    <x-tabler-arrow-up
                        class="size-4"
                        x-show="!submitting"
                    />
                    <span>{{ __('Send') }}</span>
                </x-button>
            </div>
        </div>
    </div>

    {{-- Selection is posted through these; the pills only paint them. --}}
    <input
        type="hidden"
        name="avatar_id"
        :value="selection.avatar ? selection.avatar.id : ''"
    >
    <input
        type="hidden"
        name="voice_id"
        :value="selection.voice ? selection.voice.id : ''"
    >
    <input
        type="hidden"
        name="style_id"
        :value="selection.style ? selection.style.id : ''"
    >
    <input
        type="hidden"
        name="brand_kit_id"
        :value="selection.brand ? selection.brand.id : ''"
    >

    <p
        class="mt-3 px-3 text-2xs text-rose-500"
        x-cloak
        x-show="uploadError"
        x-text="uploadError"
    ></p>

    @if (session('type') === 'error' && session('message'))
        <p class="mt-3 px-3 text-2xs text-rose-500">{{ session('message') }}</p>
    @endif

    @error('prompt')
        <p class="mt-3 px-3 text-2xs text-rose-500">{{ $message }}</p>
    @enderror

    <p class="text-label mt-2.5 px-3 pb-2 text-center text-4xs leading-relaxed">
        {{ __('Attach up to :count reference files to steer the result.', ['count' => config('video-agent.limits.max_files')]) }}
        <span x-text="` · ${prompt.length} / {{ config('video-agent.limits.prompt_max') }}`"></span>
    </p>
</form>
