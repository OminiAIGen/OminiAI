{{-- Marketing Agent copilot chat pane (left rail) --}}
<div
    class="group/copilot relative z-30 flex shrink-0 flex-col border-border bg-background transition-all duration-200 max-lg:absolute max-lg:inset-x-0 max-lg:bottom-0 max-lg:h-[calc(100dvh-8rem)] max-lg:translate-y-full max-lg:border-t max-lg:shadow-2xl max-lg:shadow-black/5 lg:w-10 lg:border-e max-lg:[&.is-open]:translate-y-0 lg:[&.is-open]:w-[380px]"
    :class="{ 'is-open': copilotOpen }"
>
    {{-- Collapse toggle --}}
    <x-button
        class="absolute z-10 grid size-[30px] place-items-center bg-background p-0 max-lg:-top-4 max-lg:end-4 max-lg:hidden max-lg:group-[&.is-open]/copilot:inline-flex lg:-end-3.5 lg:top-5"
        hover-variant="primary"
        variant="outline"
        type="button"
        @click="copilotOpen = !copilotOpen"
        ::title="copilotOpen ? '{{ __('Hide Marketing Agent') }}' : '{{ __('Show Marketing Agent') }}'"
    >
        <x-tabler-chevron-left
            class="size-4 transition-transform max-lg:hidden"
            ::class="copilotOpen ? '' : 'rotate-180'"
        />
        <x-tabler-x class="size-4 lg:hidden" />
    </x-button>

    {{-- Collapsed label --}}
    <div
        class="absolute inset-0 flex flex-col items-center justify-center gap-2"
        x-show="!copilotOpen"
        x-cloak
    >
        <x-tabler-sparkles class="size-4 text-primary" />
        <span class="text-2xs font-medium [writing-mode:vertical-rl]">{{ __('Marketing Agent') }}</span>
    </div>

    {{-- Panel content --}}
    <div
        class="flex min-h-0 flex-1 flex-col"
        x-show="copilotOpen"
        x-cloak
    >
        <div
            class="flex min-h-0 flex-1 flex-col gap-6 overflow-y-auto p-5"
            x-ref="copilotMessages"
            x-init="$nextTick(() => $refs.copilotMessages.scrollTop = $refs.copilotMessages.scrollHeight)"
        >
            {{-- Empty state --}}
            <template x-if="copilotMessages.length === 0">
                <div class="flex h-full flex-col items-start justify-center gap-3.5">
                    <x-tabler-sparkles class="size-6 text-primary" />
                    <p class="m-0 text-lg font-medium leading-[1.1em]">
                        {{ __('Describe your campaign') }}
                    </p>
                    <p class="m-0 text-pretty text-2xs opacity-85">
                        {{ __('Tell me what you want to send and to whom — I\'ll write the copy, pick the audience, and set the schedule for you.') }}
                    </p>
                    <x-button
                        variant="outline"
                        type="button"
                        @click="copilotInput = '{{ __('Create a campaign announcing our Summer Sale with 20% off.') }}'; $nextTick(() => $refs.copilotInput?.focus())"
                    >
                        {{ __('Try an example') }}
                    </x-button>
                </div>
            </template>

            {{-- Message list --}}
            <template x-for="msg in copilotMessages" :key="msg.id">
                <div
                    class="flex flex-col gap-1"
                    :class="msg.role === 'user' ? 'items-end' : 'items-start'"
                    x-show="msg.role === 'user' || (msg.steps && msg.steps.length > 0) || msg.content"
                >
                    {{-- User bubble --}}
                    <template x-if="msg.role === 'user'">
                        <div
                            class="max-w-[85%] rounded-lg bg-secondary p-4 text-secondary-foreground dark:bg-foreground/5"
                            x-init="$nextTick(() => $refs.copilotMessages.scrollTop = $refs.copilotMessages.scrollHeight)"
                        >
                            <div class="prose prose-sm whitespace-pre-wrap text-2xs leading-[1.4em] dark:prose-invert" x-text="msg.content"></div>
                        </div>
                    </template>

                    {{-- Assistant: streaming activity log --}}
                    <template x-if="msg.role === 'assistant' && msg.steps && msg.steps.length > 0">
                        <div class="prose prose-sm w-full rounded-lg border border-border p-4 text-2xs leading-[1.4em] dark:prose-invert">
                            <p class="mb-3 flex items-center gap-2 text-sm font-semibold">
                                <x-tabler-sparkles class="size-4 text-primary" />
                                {{ __('Marketing Agent') }}
                            </p>

                            <template x-for="(step, si) in msg.steps" :key="si">
                                <div x-init="$nextTick(() => $refs.copilotMessages.scrollTop = $refs.copilotMessages.scrollHeight)">
                                    {{-- Thinking --}}
                                    <template x-if="step.type === 'thinking'">
                                        <div class="my-2">
                                            <template x-if="step.status === 'pending'">
                                                <x-shimmer-text>{{ __('Thinking...') }}</x-shimmer-text>
                                            </template>
                                            <template x-if="step.status === 'done'">
                                                <div class="flex items-center gap-1.5 text-foreground/65">
                                                    <span x-text="step.durationMs !== null && step.durationMs !== undefined ? `{{ __('Thought for') }} ${formatThinkingDuration(step.durationMs)}` : `{{ __('Thought') }}`"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Streamed text --}}
                                    <template x-if="step.type === 'text'">
                                        <div x-html="renderMarkdown(step.content)"></div>
                                    </template>

                                    {{-- Tool call card --}}
                                    <template x-if="step.type === 'tool_call'">
                                        <x-card class="mb-2 last:mb-0" size="none">
                                            <button
                                                class="flex w-full items-center gap-2 px-2.5 py-2 text-start"
                                                type="button"
                                                @click="msg.steps[si] = { ...step, expanded: !step.expanded }"
                                            >
                                                <x-tabler-wand class="size-4 shrink-0 text-primary" />
                                                <span class="min-w-0 flex-1 truncate font-medium" x-text="toolCallLabel(step)"></span>
                                                <template x-if="step.status === 'pending'">
                                                    <span class="size-3 animate-spin rounded-full border border-foreground/30 border-t-foreground/60"></span>
                                                </template>
                                                <template x-if="step.status === 'success'">
                                                    <span class="flex size-4 items-center justify-center rounded-full bg-green-500/15 text-green-600 dark:text-green-400">
                                                        <x-tabler-check class="size-3" />
                                                    </span>
                                                </template>
                                                <x-tabler-chevron-down class="size-3 shrink-0 transition" ::class="step.expanded ? 'rotate-180' : ''" />
                                            </button>
                                            <template x-if="step.expanded">
                                                <div class="border-t border-border px-2.5 py-2">
                                                    <pre class="overflow-x-auto whitespace-pre-wrap text-3xs text-foreground/70" x-text="JSON.stringify(step.args, null, 2)"></pre>
                                                </div>
                                            </template>
                                        </x-card>
                                    </template>

                                    {{-- Action buttons --}}
                                    <template x-if="step.type === 'action_buttons'">
                                        <div class="flex flex-col gap-1.5 pt-0.5">
                                            <template x-for="(btn, bi) in step.buttons" :key="bi">
                                                <button
                                                    class="flex w-full items-center gap-3 rounded-xl border border-border bg-background p-3 text-start shadow-sm transition-colors hover:bg-foreground/5"
                                                    type="button"
                                                    @click="handleCopilotAction(btn.action, btn.args)"
                                                >
                                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                                        <x-tabler-arrow-right class="size-4" />
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="m-0 text-[12px] font-semibold text-heading-foreground" x-text="btn.label"></p>
                                                        <p class="m-0 text-3xs opacity-65" x-show="btn.description" x-text="(btn.description ?? '') + ' →'"></p>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Assistant: legacy bubble (loaded from DB) --}}
                    <template x-if="msg.role === 'assistant' && (!msg.steps || msg.steps.length === 0) && msg.content">
                        <div class="prose prose-sm w-full rounded-lg border border-border p-4 text-2xs leading-[1.4em] dark:prose-invert">
                            <p class="mb-3 flex items-center gap-2 text-sm font-semibold">
                                <x-tabler-sparkles class="size-4 text-primary" />
                                {{ __('Marketing Agent') }}
                            </p>
                            <div class="m-0" x-html="renderMarkdown(msg.content)"></div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Loading dots --}}
            <template x-if="copilotLoading">
                <div class="flex items-start">
                    <div class="flex items-center gap-1 rounded-xl bg-foreground/5 px-3 py-3">
                        <span class="size-1 animate-bounce-load-more rounded-full bg-foreground/30 [animation-delay:-0.3s]"></span>
                        <span class="size-1 animate-bounce-load-more rounded-full bg-foreground/30 [animation-delay:-0.15s]"></span>
                        <span class="size-1 animate-bounce-load-more rounded-full bg-foreground/30"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Composer --}}
        <div class="shrink-0 p-4">
            <div class="rounded-[22px] border border-border shadow-xl shadow-black/5 dark:border-foreground/5">
                <x-forms.input
                    class="rounded-none border-none bg-transparent p-5 !outline-none !ring-0 resize-none"
                    type="textarea"
                    rows="3"
                    x-ref="copilotInput"
                    x-model="copilotInput"
                    placeholder="{{ __('Your message') }}"
                    @keydown.meta.enter.prevent="sendCopilotMessage()"
                    @keydown.ctrl.enter.prevent="sendCopilotMessage()"
                />
                <div class="flex items-center justify-end gap-1 px-5 pb-4 pt-1">
                    <x-button
                        class="size-10 shrink-0 bg-heading-foreground p-0 text-header-background"
                        variant="primary"
                        type="button"
                        @click="sendCopilotMessage()"
                        ::disabled="copilotLoading || !copilotInput.trim()"
                    >
                        <x-tabler-send class="size-4" />
                        <span class="sr-only">{{ __('Send') }}</span>
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>
