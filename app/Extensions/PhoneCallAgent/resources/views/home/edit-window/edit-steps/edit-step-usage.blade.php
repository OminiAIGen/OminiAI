{{-- Step 3 - Phone Numbers & Usage --}}
<div
    class="col-start-1 col-end-1 row-start-1 row-end-1 transition-all lg:w-[550px]"
    data-step="3"
    x-show="editingStep === 3"
    x-transition:enter-start="opacity-0 -translate-x-3"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-3"
>
    <h2 class="mb-3.5">@lang('Phone Numbers & Usage')</h2>
    <p class="text-xs/5 opacity-60 lg:max-w-[360px]">
        @lang('View assigned phone numbers and monitor your monthly call usage.')
    </p>

    <div class="flex flex-col gap-7 pt-9">
		{{-- Provider --}}
        <div>
            <label class="form-label text-heading-foreground">@lang('Provider')</label>
            <div class="flex items-center gap-2 rounded-lg border bg-heading-foreground/[2%] px-4 py-3 text-sm capitalize">
                <x-tabler-phone class="size-4 text-primary" />
                <span x-text="activeAgent.provider || 'elevenlabs'"></span>
            </div>
        </div>

        {{-- ElevenLabs Agent ID (hidden for Twilio agents) --}}
        <template x-if="activeAgent.provider !== 'twilio'">
            <div>
                <label class="form-label text-heading-foreground">@lang('ElevenLabs Agent ID')</label>
                <div class="flex items-center gap-3 rounded-lg border bg-heading-foreground/[2%] px-4 py-3 text-sm font-mono">
                    <span
                        class="grow"
                        x-text="activeAgent.agent_id || '{{ __('Not yet created') }}'"
                    ></span>
                    <template x-if="activeAgent.agent_id">
                        <x-button
                            class="text-2xs"
                            variant="outline"
                            size="sm"
                            type="button"
                            @click="navigator.clipboard.writeText(activeAgent.agent_id); toastr.success('{{ __('Copied!') }}')"
                        >
                            <x-tabler-copy class="size-3.5" />
                        </x-button>
                    </template>
                </div>
            </div>
        </template>

        {{-- Phone number — ElevenLabs (import into workspace) & Twilio (connect own number) --}}
        <template x-if="(activeAgent.provider === 'elevenlabs' && activeAgent.agent_id) || activeAgent.provider === 'twilio'">
            <div class="flex flex-col gap-5">
                <div>
                    <label class="form-label text-heading-foreground">@lang('Phone Number')</label>

                    {{-- Current assigned number --}}
                    <template x-if="activeAgent.phone_number">
                        <div class="flex items-center gap-3 rounded-lg border bg-heading-foreground/[2%] px-4 py-3 text-sm">
                            <x-tabler-phone-check class="size-4 text-green-500" />
                            <span
                                class="grow font-mono"
                                x-text="activeAgent.phone_number"
                            ></span>
                            @unless ($app_is_demo)
                                <x-button
                                    class="text-2xs"
                                    variant="outline"
                                    size="sm"
                                    type="button"
                                    ::disabled="phoneBusy"
                                    @click="releasePhoneNumber()"
                                >
                                    <x-tabler-trash class="size-3.5" />
                                    @lang('Remove')
                                </x-button>
                            @endunless
                        </div>
                    </template>

                    <template x-if="!activeAgent.phone_number">
                        <p class="text-xs opacity-60">@lang('No phone number attached yet.')</p>
                    </template>
                </div>

                {{-- Import (ElevenLabs) / Connect (Twilio) a number (modal) --}}
                @unless ($app_is_demo)
                <x-modal id="phone-import-modal">
                    <x-slot:title>
                        <span x-text="activeAgent.provider === 'twilio' ? '{{ __('Connect a Twilio Number') }}' : '{{ __('Import a Phone Number') }}'"></span>
                    </x-slot:title>

                    <x-slot:trigger class="w-full" variant="outline" type="button">
                        <x-tabler-download class="size-4" />
                        <span x-text="activeAgent.provider === 'twilio' ? '{{ __('Connect Phone Number') }}' : '{{ __('Import Phone Number') }}'"></span>
                    </x-slot:trigger>

                    <x-slot:modal>
                        <div
                            class="flex flex-col gap-4"
                            x-data="{ form: $store.phoneCallAgentEditor.phoneForm }"
                        >
                            <p class="text-2xs opacity-60">
                                <span x-show="activeAgent.provider === 'twilio'">@lang('Connect your own Twilio number. We configure its webhooks automatically. Credentials are stored on this agent only.')</span>
                                <span x-show="activeAgent.provider !== 'twilio'">@lang('Import a number into your ElevenLabs workspace and attach it to this agent.')</span>
                            </p>

                            {{-- Source selector (ElevenLabs only — Twilio agents always use their own Twilio number) --}}
                            <div x-show="activeAgent.provider !== 'twilio'">
                                <x-forms.input
                                    class:label="text-heading-foreground"
                                    label="{{ __('Import From') }}"
                                    type="select"
                                    name="import_type"
                                    x-model="form.import_type"
                                >
                                    <option value="twilio">@lang('Twilio')</option>
                                    <option value="sip_trunk">@lang('SIP Trunk')</option>
                                    <option value="exotel">@lang('Exotel')</option>
                                </x-forms.input>
                            </div>

                            <x-forms.input
                                class:label="text-heading-foreground"
                                label="{{ __('Phone Number') }}"
                                type="text"
                                name="phone_number"
                                x-model="form.phone_number"
                                placeholder="+14155552671"
                            />

                            {{-- Twilio source --}}
                            <template x-if="form.import_type === 'twilio'">
                                <div class="flex flex-col gap-4">
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Twilio Account SID') }}"
                                        type="text"
                                        name="twilio_account_sid"
                                        x-model="form.twilio_account_sid"
                                        placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                                    />
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Twilio Auth Token') }}"
                                        type="password"
                                        name="twilio_auth_token"
                                        x-model="form.twilio_auth_token"
                                    />
                                </div>
                            </template>

                            {{-- SIP Trunk source --}}
                            <template x-if="form.import_type === 'sip_trunk'">
                                <div class="flex flex-col gap-4">
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('SIP Address') }}"
                                        type="text"
                                        name="sip_address"
                                        x-model="form.sip_address"
                                        placeholder="sip.example.com"
                                    />
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Transport') }}"
                                        type="select"
                                        name="sip_transport"
                                        x-model="form.sip_transport"
                                    >
                                        <option value="auto">@lang('Auto')</option>
                                        <option value="udp">UDP</option>
                                        <option value="tcp">TCP</option>
                                        <option value="tls">TLS</option>
                                    </x-forms.input>
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Username (optional)') }}"
                                        type="text"
                                        name="sip_username"
                                        x-model="form.sip_username"
                                    />
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Password (optional)') }}"
                                        type="password"
                                        name="sip_password"
                                        x-model="form.sip_password"
                                    />
                                </div>
                            </template>

                            {{-- Exotel source --}}
                            <template x-if="form.import_type === 'exotel'">
                                <div class="flex flex-col gap-4">
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Exotel Account SID') }}"
                                        type="text"
                                        name="exotel_account_sid"
                                        x-model="form.exotel_account_sid"
                                    />
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('API Key') }}"
                                        type="text"
                                        name="exotel_api_key"
                                        x-model="form.exotel_api_key"
                                    />
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('API Token') }}"
                                        type="password"
                                        name="exotel_api_token"
                                        x-model="form.exotel_api_token"
                                    />
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('API Subdomain') }}"
                                        type="select"
                                        name="exotel_subdomain"
                                        x-model="form.exotel_subdomain"
                                    >
                                        <option value="api.exotel.com">api.exotel.com</option>
                                        <option value="api.in.exotel.com">api.in.exotel.com</option>
                                    </x-forms.input>
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('App ID') }}"
                                        type="text"
                                        name="exotel_app_id"
                                        x-model="form.exotel_app_id"
                                    />
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Applet URL (optional)') }}"
                                        type="text"
                                        name="exotel_applet_url"
                                        x-model="form.exotel_applet_url"
                                    />
                                </div>
                            </template>

                            {{-- Label (ElevenLabs only) --}}
                            <div x-show="activeAgent.provider !== 'twilio'">
                                <x-forms.input
                                    class:label="text-heading-foreground"
                                    label="{{ __('Label (optional)') }}"
                                    type="text"
                                    name="label"
                                    x-model="form.label"
                                />
                            </div>

                            <x-button
                                class="w-full"
                                type="button"
                                ::disabled="$store.phoneCallAgentEditor.phoneBusy"
                                @click="$store.phoneCallAgentEditor.importPhoneNumber()"
                            >
                                <x-tabler-download class="size-4" />
                                <span x-text="activeAgent.provider === 'twilio' ? '{{ __('Connect & Configure') }}' : '{{ __('Import & Attach') }}'"></span>
                            </x-button>
                        </div>
                    </x-slot:modal>
                </x-modal>

                {{-- Pick an existing number --}}
                <template x-if="phoneNumbersList.length">
                    <div class="flex flex-col gap-2">
                        <label class="form-label text-heading-foreground">@lang('Your Imported Numbers')</label>
                        <template
                            x-for="number in phoneNumbersList"
                            :key="number.phone_number_id"
                        >
                            <div class="flex items-center gap-3 rounded-lg border px-4 py-3 text-sm">
                                <x-tabler-phone class="size-4 text-primary" />
                                <span
                                    class="grow font-mono"
                                    x-text="number.phone_number"
                                ></span>
                                <x-button
                                    class="text-2xs"
                                    variant="outline"
                                    size="sm"
                                    type="button"
                                    ::disabled="phoneBusy || number.phone_number_id === activeAgent.phone_number_id"
                                    @click="assignPhoneNumber(number)"
                                >
                                    <span x-text="number.phone_number_id === activeAgent.phone_number_id ? '{{ __('Attached') }}' : '{{ __('Attach') }}'"></span>
                                </x-button>
                            </div>
                        </template>
                    </div>
                </template>
                @endunless
            </div>
        </template>

        {{-- Follow-Up Calls --}}
        @unless ($app_is_demo)
        @if ($followUpEnabled ?? true)
        <div class="flex flex-col gap-3 border-t border-card-border pt-7">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <label class="form-label text-heading-foreground">@lang('Follow-Up Calls')</label>
                    <p class="text-xs opacity-60 lg:max-w-[320px]">
                        @lang('Automatically call customers around their bookings (reminders, surveys) or after an inbound call.')
                    </p>
                </div>
                <span
                    class="shrink-0 rounded-full bg-primary/10 px-2.5 py-1 text-2xs font-medium text-primary"
                    x-show="(activeAgent.follow_up_rules || []).length"
                    x-text="`${(activeAgent.follow_up_rules || []).length} {{ __('rule(s)') }}`"
                ></span>
            </div>

            <template x-if="!activeAgent.phone_number">
                <p class="text-2xs text-amber-500">@lang('Attach a phone number above so follow-up calls can be placed.')</p>
            </template>

            <x-modal id="follow-up-modal" class:modal-content="max-w-xl" disable-focus="true">
                <x-slot:title>@lang('Follow-Up Call Rules')</x-slot:title>

                <x-slot:trigger class="w-full" variant="outline" type="button">
                    <x-tabler-phone-outgoing class="size-4" />
                    @lang('Configure Follow-Up Calls')
                </x-slot:trigger>

                <x-slot:modal>
                    <div class="flex flex-col gap-4">
                        <p class="text-2xs opacity-60">
                            @lang('Each rule places one outbound call when its condition is met. Booking triggers require the Booking Assistant (Step 1) to be enabled. Use {name}, {time} and {date} placeholders in the instruction.')
                        </p>

                        <template x-if="!(activeAgent.follow_up_rules || []).length">
                            <div class="rounded-lg border border-dashed border-card-border px-4 py-6 text-center text-xs opacity-60">
                                @lang('No follow-up rules yet.')
                            </div>
                        </template>

                        <template x-for="(rule, index) in (activeAgent.follow_up_rules || [])" :key="index">
                            <div class="flex flex-col gap-3 rounded-lg border border-card-border bg-heading-foreground/[2%] p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <x-forms.input
                                        class="h-[18px] w-[34px] [background-size:0.625rem]"
                                        class:label="text-heading-foreground flex-row-reverse justify-end gap-2"
                                        label="{{ __('Enabled') }}"
                                        name="follow_up_enabled"
                                        size="lg"
                                        type="checkbox"
                                        switcher
                                        x-model.boolean="rule.enabled"
                                    />
                                    <x-button
                                        class="text-2xs"
                                        variant="ghost"
                                        size="sm"
                                        type="button"
                                        @click="removeFollowUpRule(index)"
                                    >
                                        <x-tabler-trash class="size-3.5" />
                                    </x-button>
                                </div>

                                <x-forms.input
                                    class:label="text-heading-foreground"
                                    label="{{ __('When') }}"
                                    type="select"
                                    name="follow_up_trigger"
                                    x-model="rule.trigger"
                                >
                                    <option value="before_booking">@lang('Before the booking')</option>
                                    <option value="after_booking">@lang('After the booking')</option>
                                    <option value="after_call">@lang('After an inbound call')</option>
                                    <option value="on_cancel">@lang('When a booking is cancelled')</option>
                                </x-forms.input>

                                <div class="grid grid-cols-2 gap-3" x-show="rule.trigger !== 'on_cancel'">
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Offset') }}"
                                        type="number"
                                        min="0"
                                        name="follow_up_offset_value"
                                        x-model.number="rule.offset_value"
                                    />
                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Unit') }}"
                                        type="select"
                                        name="follow_up_offset_unit"
                                        x-model="rule.offset_unit"
                                    >
                                        <option value="minute">@lang('Minutes')</option>
                                        <option value="hour">@lang('Hours')</option>
                                        <option value="day">@lang('Days')</option>
                                    </x-forms.input>
                                </div>

                                <x-forms.input
                                    class:label="text-heading-foreground"
                                    label="{{ __('What the assistant should say') }}"
                                    type="textarea"
                                    rows="3"
                                    name="follow_up_instruction"
                                    x-model="rule.instruction"
                                    placeholder="{{ __('Remind {name} about their appointment on {date} at {time} and confirm they will attend.') }}"
                                />
                            </div>
                        </template>

                        <x-button
                            class="w-full"
                            variant="outline"
                            type="button"
                            @click="addFollowUpRule()"
                        >
                            <x-tabler-plus class="size-4" />
                            @lang('Add Rule')
                        </x-button>

                        <x-button
                            class="w-full"
                            type="button"
                            ::disabled="submittingData"
                            @click="saveFollowUpRules()"
                        >
                            <x-tabler-device-floppy class="size-4" />
                            @lang('Save Follow-Up Rules')
                        </x-button>
                    </div>
                </x-slot:modal>
            </x-modal>
        </div>
        @endif
        @endunless
    </div>
</div>
