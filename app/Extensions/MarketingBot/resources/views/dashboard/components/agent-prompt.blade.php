@php
    $marketingAgentEnabled = auth()->check()
        ? (new \App\Extensions\MarketingBot\System\Services\MarketingBotLimitService(auth()->user()))->isAgentEnabled()
        : false;
@endphp

@if ($marketingAgentEnabled)
    <div
        class="lqd-marketing-agent-dash mb-12"
        x-data="marketingAgentDash()"
    >
        <div class="container flex max-w-[740px] flex-col gap-7">
            <section class="flex flex-wrap gap-x-4">
                <div class="grow">
                    <a
                        class="mb-4 inline-flex items-center gap-2 rounded-full border border-border py-1.5 pe-5 ps-1.5 text-[13px] font-medium text-foreground/80 transition hover:border-foreground/20 hover:bg-foreground/5"
                        href="{{ route('dashboard.user.marketing-bot.settings.index') }}"
                    >
                        <span class="inline-grid size-[34px] place-items-center rounded-full bg-foreground/5 text-center">
                            <x-tabler-settings class="size-5 text-foreground/70" />
                        </span>
                        {{ __('Manage Settings') }}
                    </a>

                    <h2 class="text-[30px] font-medium max-md:mb-5">
                        <span class="block opacity-35">
                            {{ __('Broadcast messages') }}
                        </span>
                        <span>
                            {{ __('to your audience instantly.') }}
                        </span>
                    </h2>
                </div>

                <div
                    class="flex w-[230px] justify-center self-end max-md:order-first max-md:mb-7 max-md:[mask-image:linear-gradient(to_bottom,black_70%,transparent_100%)] max-sm:w-[60vw]"
                    aria-hidden="true"
                >
                    <img
                        class="h-auto w-full"
                        src="{{ asset('vendor/marketing-bot/images/avatar.png') }}"
                        alt="{{ __('Marketing Agent') }}"
                        aria-hidden="true"
                    >
                </div>

                <form
                    class="w-full overflow-hidden rounded-[22px] border border-transparent shadow-2xl shadow-black/[25%] dark:border-foreground/5"
                    method="POST"
                    action="{{ route('dashboard.user.marketing-bot.agent.start') }}"
                >
                    @csrf
                    <input
                        type="hidden"
                        name="channel"
                        value="whatsapp"
                        x-bind:value="channel"
                    >
                    <x-forms.input
                        class="w-full resize-none rounded-none border-none px-7 py-4 focus-visible:outline-none focus-visible:ring-0"
                        rows="4"
                        type="textarea"
                        name="message"
                        required
                        placeholder="{{ __('Describe the marketing campaign you\'d like to create...') }}"
                        x-model="input"
                        @keydown.meta.enter.prevent="$el.closest('form').requestSubmit()"
                        @keydown.ctrl.enter.prevent="$el.closest('form').requestSubmit()"
                    ></x-forms.input>

                    <div class="flex items-center justify-between gap-2 px-7 pb-4">
                        <div class="flex items-center gap-1.5">
                            <button
                                class="flex items-center gap-1.5 rounded-full border border-border px-3 py-1.5 text-2xs font-medium transition-colors [&.active]:border-primary [&.active]:bg-primary/10 [&.active]:text-primary"
                                :class="{ active: channel === 'whatsapp' }"
                                type="button"
                                @click="channel = 'whatsapp'"
                            >
                                <img
                                    class="size-4"
                                    src="{{ asset('vendor/marketing-bot/images/whatsapp.png') }}"
                                    alt="WhatsApp"
                                >
                                {{ __('WhatsApp') }}
                            </button>
                            <button
                                class="flex items-center gap-1.5 rounded-full border border-border px-3 py-1.5 text-2xs font-medium transition-colors [&.active]:border-primary [&.active]:bg-primary/10 [&.active]:text-primary"
                                :class="{ active: channel === 'telegram' }"
                                type="button"
                                @click="channel = 'telegram'"
                            >
                                <img
                                    class="size-4"
                                    src="{{ asset('vendor/marketing-bot/images/telegram.png') }}"
                                    alt="Telegram"
                                >
                                {{ __('Telegram') }}
                            </button>
                        </div>

                        <x-button
                            class="grid size-11 place-items-center rounded-full bg-heading-foreground text-background hover:scale-105 hover:shadow-xl hover:shadow-black/5"
                            hover-variant="primary"
                            size="none"
                            type="submit"
                            ::disabled="!input.trim()"
                        >
                            <svg
                                width="15"
                                height="12"
                                viewBox="0 0 15 12"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path d="M0 12V7.5L6 6L0 4.5V0L14.25 6L0 12Z" />
                            </svg>
                            <span class="sr-only">{{ __('Create Campaign') }}</span>
                        </x-button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    @push('script')
        <script>
            function marketingAgentDash() {
                return {
                    channel: 'whatsapp',
                    input: '',
                };
            }
        </script>
    @endpush
@endif
