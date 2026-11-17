{{--
    Session history rail on the thread page. The per-row delete finally gives
    `sessions.destroy` a UI. The rail carries its own padding because the page
    frame is edge-to-edge so the thread can scroll inside it.
--}}
<aside class="flex w-full shrink-0 flex-col gap-4 border-card-border bg-card-background/50 px-3 py-3 max-lg:border-b sm:px-4 lg:h-full lg:w-[288px] lg:border-e lg:px-4 lg:py-6">
    <x-button
        class="w-full justify-start gap-2.5 rounded-xl px-3 py-2.5 shadow-sm"
        href="{{ route('dashboard.user.video-agent.index') }}"
        variant="outline"
        size="none"
    >
        <span class="flex size-7 items-center justify-center rounded-lg bg-primary/10 text-primary">
            <x-tabler-plus class="size-4" />
        </span>
        <span class="flex flex-col items-start leading-tight">
            <span>{{ __('New video') }}</span>
            <span class="text-[10px] font-normal text-label">{{ __('Start a new session') }}</span>
        </span>
    </x-button>

    <div class="-me-2 flex min-h-0 max-h-[8.5rem] gap-2 overflow-auto pe-2 lg:max-h-none lg:grow lg:flex-col lg:gap-5">
        @foreach ([true => __('Today'), false => __('Earlier')] as $today => $heading)
            <div
                class="shrink-0"
                x-cloak
                x-show="sessions.some((item) => item.today === {{ $today ? 'true' : 'false' }})"
            >
                <h4 class="mb-2 px-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-label max-lg:hidden">{{ $heading }}</h4>

                <ul class="m-0 flex list-none gap-1 p-0 lg:flex-col">
                    <template
                        x-for="session in sessions.filter((item) => item.today === {{ $today ? 'true' : 'false' }})"
                        :key="session.uuid"
                    >
                        <li class="group/row flex w-52 items-center gap-2.5 rounded-xl border border-transparent px-2.5 py-2 transition hover:border-card-border hover:bg-background focus-within:border-primary/30 focus-within:bg-background lg:w-auto [&.current]:border-primary/15 [&.current]:bg-primary/[0.06]"
                            :class="{ 'current': session.uuid === uuid }"
                        >
                            <span
                                class="size-2 shrink-0 rounded-full ring-4 ring-transparent"
                                :class="{
                                    'bg-emerald-500 ring-emerald-500/10': session.status === 'completed',
                                    'bg-rose-500 ring-rose-500/10': session.status === 'failed',
                                    'bg-orange-500 ring-orange-500/10': session.needs_user,
                                    'bg-foreground/25': session.status === 'stopped',
                                    'animate-pulse bg-primary ring-primary/10': !session.terminal && !session.needs_user,
                                }"
                            ></span>

                            <a
                                class="min-w-0 grow truncate rounded-sm text-[11px] font-medium outline-none focus-visible:ring-2 focus-visible:ring-primary/30"
                                :href="session.url"
                                :title="session.title"
                                x-text="session.title"
                            ></a>

                            <button
                                class="shrink-0 rounded-md p-1 text-label opacity-0 transition hover:bg-rose-500/10 hover:text-rose-500 focus:opacity-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-rose-500 group-hover/row:opacity-100 max-lg:opacity-100"
                                type="button"
                                @click.prevent="remove(session)"
                            >
                                <x-tabler-trash class="size-3.5" />
                                <span class="sr-only">{{ __('Delete') }}</span>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        @endforeach

        <p
            class="m-0 px-2 text-[11px] text-label"
            x-cloak
            x-show="!sessions.length"
        >{{ __('No sessions yet.') }}</p>
    </div>
</aside>
