{{--
    Script Writer. HeyGen has no script-generation endpoint, so this runs on
    MagicAI's own word model and the result replaces the composer prompt.
--}}
@php
    $videoAgentScriptMax = \App\Extensions\VideoAgent\System\Http\Controllers\ScriptWriterController::TOPIC_MAX;

    $videoAgentScriptIdeas = [
        __('a product demo'),
        __('an onboarding walkthrough'),
        __('a customer testimonial'),
        __('a 30-second social ad'),
    ];
@endphp

<div
    class="contents"
    x-data="videoAgentScriptWriter"
>
    <x-modal
        class:close-btn="size-7 [&_svg]:size-4"
        class:modal-body="p-0"
        class:modal-content="w-[min(100%,520px)] min-w-[min(100%,520px)]"
        class:modal-head="border-b-0 px-5 pb-0 pt-4"
        class:modal-title="flex items-center gap-2 text-base font-semibold"
        id="va-script-modal"
        disable-focus
    >
        <x-slot:title>
            <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                <x-tabler-sparkles class="size-4" />
            </span>
            {{ __('Script Writer') }}
        </x-slot:title>

        <x-slot:modal>
            <div class="flex flex-col gap-3 px-5 pb-5 pt-2">
                {{-- Demo mode disables the button; say so instead of leaving it dead. --}}
                <x-alert
                    variant="warn"
                    size="sm"
                    icon="tabler-lock"
                    x-cloak
                    x-show="demo"
                >
                    {{ __('This feature is disabled in demo mode.') }}
                </x-alert>

                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-2xs font-medium"
                        for="va-script-topic"
                    >{{ __('Write a script for…') }}</label>

                    <textarea
                        class="min-h-24 w-full resize-none rounded-card border border-input-border bg-background px-3 py-2.5 text-2xs text-foreground transition-colors placeholder:text-label focus:border-primary focus:outline-none focus:ring-0 disabled:opacity-60"
                        id="va-script-topic"
                        rows="4"
                        maxlength="{{ $videoAgentScriptMax }}"
                        placeholder="{{ __('A walkthrough of our checkout flow, aimed at new customers, friendly but brief.') }}"
                        x-ref="topic"
                        x-model="topic"
                        :disabled="generating || demo"
                        @keydown.enter="if ($event.metaKey || $event.ctrlKey) { $event.preventDefault(); generate(); }"
                    ></textarea>

                    <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-label">
                        {{-- The shortcut is worth nothing if nobody is told about it. --}}
                        <span class="inline-flex items-center gap-1">
                            <kbd class="rounded border border-input-border px-1 py-px font-sans text-[10px]">⌘</kbd>
                            <kbd class="rounded border border-input-border px-1 py-px font-sans text-[10px]">↵</kbd>
                            {{ __('to generate') }}
                        </span>

                        <span
                            class="tabular-nums [&.over]:text-rose-500"
                            :class="{ 'over': topic.length >= {{ $videoAgentScriptMax }} }"
                            x-text="`${topic.length} / {{ $videoAgentScriptMax }}`"
                        ></span>
                    </div>
                </div>

                {{-- A blank box is a cold start; these fill it in one tap. --}}
                <div
                    class="flex flex-wrap gap-1.5"
                    x-cloak
                    x-show="!topic.trim() && !generating"
                >
                    @foreach ($videoAgentScriptIdeas as $idea)
                        <button
                            class="rounded-full border border-input-border px-2.5 py-1 text-[11px] text-label transition-colors hover:border-primary hover:text-foreground"
                            type="button"
                            @click="topic = {{ Js::from($idea) }}; $refs.topic.focus()"
                        >{{ $idea }}</button>
                    @endforeach
                </div>

                <x-alert
                    variant="danger"
                    size="sm"
                    icon="tabler-alert-triangle"
                    x-cloak
                    x-show="error"
                >
                    <span x-text="error"></span>
                </x-alert>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-card-border pt-3">
                    <p class="m-0 text-[11px] text-label">
                        {{ __('Replaces whatever is in the prompt, and uses your AI credits.') }}

                        {{-- The composer's length is sent with the request, so show which one. --}}
                        <span
                            class="ms-1 inline-flex items-center gap-1 rounded-full bg-surface px-2 py-0.5 text-[10px]"
                            x-cloak
                            x-show="targetLength"
                        >
                            <x-tabler-clock class="size-3" />
                            <span x-text="targetLength"></span>
                        </span>
                    </p>

                    <x-button
                        type="button"
                        size="sm"
                        ::disabled="!canGenerate"
                        @click="generate()"
                    >
                        <x-tabler-loader-2
                            class="size-4 animate-spin"
                            x-cloak
                            x-show="generating"
                        />
                        <x-tabler-sparkles
                            class="size-4"
                            x-show="!generating"
                        />
                        <span x-text="generating ? '{{ __('Generating…') }}' : '{{ __('Generate') }}'"></span>
                    </x-button>
                </div>
            </div>
        </x-slot:modal>
    </x-modal>
</div>
