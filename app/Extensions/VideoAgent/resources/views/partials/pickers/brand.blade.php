{{-- Brand kits and glossaries. Picking a kit fills the composer's brand pill. --}}
<div
    class="contents"
    x-data="videoAgentBrand"
>
    <x-modal
        class:close-btn="size-7 [&_svg]:size-4"
        class:modal-body="p-0 flex min-h-0 grow flex-col"
        class:modal-container="max-w-none p-0 flex min-h-0 grow flex-col"
        class:modal-content="w-[min(100%,960px)] h-[min(760px,calc(100vh-3rem))] flex flex-col overflow-hidden"
        class:modal-head="px-6 pt-5 pb-0 border-b-0"
        class:modal-title="text-base font-semibold"
        id="va-brand-modal"
        disable-focus
        title="{{ __('Brand') }}"
    >
        <x-slot:modal>
            <div class="flex min-h-0 grow flex-col px-6 pb-6">
                @include('video-agent::partials.demo-guard')

                <div class="mb-5 flex items-center justify-between gap-3">
                    <p class="m-0 text-2xs text-label">
                        {{ __('Define your identity once and the agent applies it to graphics, text and colors.') }}
                    </p>

                    <button
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-button border border-input-border px-3 py-1.5 text-2xs text-label transition hover:border-primary hover:text-foreground disabled:opacity-50"
                        type="button"
                        ::disabled="loading || demo"
                        @click="refresh()"
                    >
                        <x-tabler-refresh
                            class="size-3.5"
                            ::class="loading && 'animate-spin'"
                        />
                        {{ __('Refresh') }}
                    </button>
                </div>

                <div class="-mx-1 grid min-h-0 grow gap-6 overflow-y-auto px-1 lg:grid-cols-2">
                    {{-- Kits --}}
                    <div>
                        <h4 class="mb-3 text-2xs font-semibold uppercase tracking-wide text-label">{{ __('Brand kits') }}</h4>

                        <ul class="m-0 mb-4 flex list-none flex-col gap-2 p-0">
                            <template
                                x-for="kit in kits"
                                :key="kit.id"
                            >
                                <li class="flex items-center gap-3 rounded-card border border-card-border p-3">
                                    <span class="flex size-8 shrink-0 items-center justify-center overflow-hidden rounded-full border border-card-border bg-surface">
                                        <template x-if="kit.logo">
                                            <img
                                                class="size-full object-contain"
                                                :src="kit.logo"
                                                alt=""
                                            >
                                        </template>

                                        <template x-if="!kit.logo">
                                            <x-tabler-color-swatch class="size-4 text-label" />
                                        </template>
                                    </span>

                                    {{-- Renaming borrows the row rather than opening a dialog for one field. --}}
                                    <template x-if="renaming === kit.id">
                                        <span class="contents">
                                            <input
                                                class="form-input h-9 min-w-0 grow text-2xs"
                                                type="text"
                                                x-model="renameValue"
                                                x-init="$nextTick(() => $el.focus())"
                                                @keydown.enter.prevent="saveRename(kit)"
                                                @keydown.escape.prevent="renaming = null"
                                            >

                                            <button
                                                class="flex size-7 shrink-0 items-center justify-center rounded-button text-label transition hover:bg-foreground/5 hover:text-foreground disabled:opacity-40"
                                                type="button"
                                                ::disabled="saving || demo || !renameValue.trim()"
                                                @click="saveRename(kit)"
                                            >
                                                <x-tabler-check class="size-3.5" />
                                                <span class="sr-only">{{ __('Save') }}</span>
                                            </button>

                                            <button
                                                class="flex size-7 shrink-0 items-center justify-center rounded-button text-label transition hover:bg-foreground/5 hover:text-foreground"
                                                type="button"
                                                @click="renaming = null"
                                            >
                                                <x-tabler-x class="size-3.5" />
                                                <span class="sr-only">{{ __('Cancel') }}</span>
                                            </button>
                                        </span>
                                    </template>

                                    <button
                                        class="flex min-w-0 grow flex-col items-start gap-1 text-start"
                                        type="button"
                                        x-show="renaming !== kit.id"
                                        @click="choose(kit)"
                                    >
                                        <span
                                            class="truncate text-2xs font-medium"
                                            x-text="kit.name"
                                        ></span>

                                        <span
                                            class="inline-flex items-center gap-1"
                                            x-show="(kit.colors || []).length"
                                        >
                                            <template
                                                x-for="(color, index) in (kit.colors || []).slice(0, 5)"
                                                :key="index"
                                            >
                                                <span
                                                    class="size-3 rounded-full border border-card-border"
                                                    :style="`background: ${color}`"
                                                ></span>
                                            </template>
                                        </span>

                                        <span
                                            class="truncate text-[11px] text-label"
                                            x-show="kit.status === 'loading'"
                                        >{{ __('Still reading the site…') }}</span>

                                        <span
                                            class="truncate text-[11px] text-label"
                                            x-show="kit.status !== 'loading' && kit.font"
                                            x-text="kit.font"
                                        ></span>
                                    </button>

                                    {{--
                                        One HeyGen workspace serves every user
                                        of this app, so only the kit's creator
                                        gets these. Kits that arrived through a
                                        sync belong to the workspace and stay
                                        read-only.
                                    --}}
                                    <span
                                        class="flex shrink-0 items-center gap-1"
                                        x-show="kit.owned && renaming !== kit.id"
                                    >
                                        <button
                                            class="flex size-7 items-center justify-center rounded-button text-label transition hover:bg-foreground/5 hover:text-foreground"
                                            type="button"
                                            :disabled="demo"
                                            @click="renameKit(kit)"
                                        >
                                            <x-tabler-pencil class="size-3.5" />
                                            <span class="sr-only">{{ __('Rename') }}</span>
                                        </button>

                                        <button
                                            class="flex size-7 items-center justify-center rounded-button text-label transition hover:bg-rose-500/10 hover:text-rose-500"
                                            type="button"
                                            :disabled="demo"
                                            @click="deleteKit(kit)"
                                        >
                                            <x-tabler-trash class="size-3.5" />
                                            <span class="sr-only">{{ __('Delete') }}</span>
                                        </button>
                                    </span>
                                </li>
                            </template>
                        </ul>

                        <p
                            class="mb-4 text-2xs text-label"
                            x-cloak
                            x-show="!loading && kits.length === 0"
                        >{{ __('No brand kits yet.') }}</p>

                        {{--
                            HeyGen builds the kit from a website: it crawls the
                            URL and pulls out the colors, logos and fonts. There
                            is no endpoint that takes those values directly.
                        --}}
                        <div class="flex flex-col gap-3 rounded-card border border-dashed border-card-border p-4">
                            <x-forms.input
                                label="{{ __('Website URL') }}"
                                placeholder="https://acme.com"
                                type="url"
                                x-model="form.url"
                            />

                            <x-forms.input
                                label="{{ __('Kit name') }}"
                                placeholder="{{ __('Optional — defaults to the detected brand') }}"
                                x-model="form.name"
                            />

                            <p
                                class="m-0 text-[11px] text-rose-500"
                                x-cloak
                                x-show="error"
                                x-text="error"
                            ></p>

                            <x-button
                                type="button"
                                variant="outline"
                                size="sm"
                                ::disabled="saving || demo || !form.url.trim()"
                                @click="createKit()"
                            >
                                <x-tabler-plus class="size-4" />
                                <span x-text="saving ? '{{ __('Building…') }}' : '{{ __('Build from website') }}'"></span>
                            </x-button>

                            <p class="m-0 text-[11px] text-label">
                                {{ __('The site is read to extract colors, logos and fonts. This takes a moment.') }}
                            </p>
                        </div>
                    </div>

                    {{--
                        Glossaries. A kit controls how a video looks; a glossary
                        controls how it sounds — each term gets the respelling
                        the synthesizer should say. HeyGen applies it to
                        templates, Studio and translation, not to Video Agent,
                        so it is picked on the template render form.
                    --}}
                    <div>
                        <h4 class="mb-3 text-2xs font-semibold uppercase tracking-wide text-label">{{ __('Glossaries') }}</h4>

                        <p class="mb-3 text-[11px] text-label">
                            {{ __('Teach the voice how your product names are said. Applied when you render a template — Video Agent sessions use a brand kit instead.') }}
                        </p>

                        <ul class="m-0 mb-4 flex list-none flex-col gap-2 p-0">
                            <template
                                x-for="glossary in glossaries"
                                :key="glossary.id"
                            >
                                <li class="rounded-card border border-card-border p-3">
                                    <div
                                        class="text-2xs font-medium"
                                        x-text="glossary.name"
                                    ></div>
                                    <div
                                        class="mt-0.5 text-[11px] text-label"
                                        x-text="`${(glossary.terms || []).length} {{ __('terms') }}`"
                                    ></div>

                                    <div
                                        class="mt-2 flex flex-wrap gap-1"
                                        x-show="(glossary.terms || []).length"
                                    >
                                        <template
                                            x-for="(term, index) in (glossary.terms || []).slice(0, 6)"
                                            :key="index"
                                        >
                                            <span
                                                class="rounded-full bg-surface px-2 py-0.5 text-[10px] text-label"
                                                x-text="`${term.term} → ${term.pronunciation}`"
                                            ></span>
                                        </template>
                                    </div>
                                </li>
                            </template>
                        </ul>

                        <p
                            class="mb-4 text-2xs text-label"
                            x-cloak
                            x-show="!loading && glossaries.length === 0"
                        >{{ __('No glossaries yet.') }}</p>

                        <div class="flex flex-col gap-3 rounded-card border border-dashed border-card-border p-4">
                            <x-forms.input
                                label="{{ __('Glossary name') }}"
                                placeholder="{{ __('Product names') }}"
                                x-model="glossaryForm.name"
                            />

                            <template
                                x-for="(term, index) in glossaryForm.terms"
                                :key="index"
                            >
                                <div class="flex items-end gap-2">
                                    <label class="flex min-w-0 grow flex-col gap-1">
                                        <span
                                            class="text-[11px] text-label"
                                            x-show="index === 0"
                                        >{{ __('Written') }}</span>
                                        <input
                                            class="form-input h-9 text-2xs"
                                            type="text"
                                            placeholder="Acme"
                                            x-model="term.term"
                                        >
                                    </label>

                                    <label class="flex min-w-0 grow flex-col gap-1">
                                        <span
                                            class="text-[11px] text-label"
                                            x-show="index === 0"
                                        >{{ __('Spoken') }}</span>
                                        <input
                                            class="form-input h-9 text-2xs"
                                            type="text"
                                            placeholder="ak-me"
                                            x-model="term.pronunciation"
                                        >
                                    </label>

                                    <button
                                        class="flex size-9 shrink-0 items-center justify-center rounded-button text-label transition hover:bg-foreground/5 hover:text-foreground disabled:opacity-40"
                                        type="button"
                                        ::disabled="glossaryForm.terms.length === 1"
                                        @click="removeTerm(index)"
                                    >
                                        <x-tabler-x class="size-3.5" />
                                        <span class="sr-only">{{ __('Remove') }}</span>
                                    </button>
                                </div>
                            </template>

                            <button
                                class="self-start text-2xs text-label underline-offset-2 transition hover:text-foreground hover:underline"
                                type="button"
                                @click="addTerm()"
                            >{{ __('Add another term') }}</button>

                            <p
                                class="m-0 text-[11px] text-rose-500"
                                x-cloak
                                x-show="glossaryError"
                                x-text="glossaryError"
                            ></p>

                            <x-button
                                type="button"
                                variant="outline"
                                size="sm"
                                ::disabled="savingGlossary || demo || !canSaveGlossary"
                                @click="createGlossary()"
                            >
                                <x-tabler-plus class="size-4" />
                                <span x-text="savingGlossary ? '{{ __('Saving…') }}' : '{{ __('Create glossary') }}'"></span>
                            </x-button>

                            <p class="m-0 text-[11px] text-label">
                                {{ __('Changes the audio only. Captions keep the written spelling.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end border-t border-card-border pt-4">
                    <button
                        class="text-2xs text-label underline-offset-2 transition hover:text-foreground hover:underline"
                        type="button"
                        @click="clear()"
                    >{{ __('Use no brand kit') }}</button>
                </div>
            </div>
        </x-slot:modal>
    </x-modal>
</div>
