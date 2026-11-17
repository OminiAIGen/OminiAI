@extends('panel.layout.app', ['disable_titlebar' => true])
@section('title', __('Auto Edit Videos'))

@section('content')
    <div class="py-10">
        <div class="container flex flex-col gap-7">
            <div class="mx-auto flex w-full max-w-[740px] flex-col gap-7">
                <section class="flex flex-wrap gap-x-4">
                    <div class="grow">
                        <a
                            class="mb-4 inline-flex items-center gap-2 rounded-full border py-1.5 pe-4 ps-1.5 text-[12px] font-medium text-foreground/80 transition hover:border-foreground/20 hover:bg-foreground/5"
                            href="{{ $latestSessionUrl ?? route('dashboard.user.auto-edit-videos.index') }}"
                        >
                            <span class="inline-grid size-[34px] place-items-center rounded-full bg-foreground/5 text-center">
                                <x-tabler-scissors class="size-5 text-foreground/70" />
                            </span>
                            <span>{{ $sessionsCount ?? 0 }}</span>
                            {{ __('Sessions') }}
                        </a>

                        <h2 class="text-[30px] font-medium max-md:mb-5">
                            <span class="block opacity-35">
                                {{ __('Hey :username', ['username' => $userName ?? '']) }},
                            </span>
                            <span>
                                {{ __('Describe your edit.') }}
                            </span>
                        </h2>
                    </div>

                    <div
                        class="flex w-[230px] justify-center self-end max-md:order-first max-md:mb-7 max-md:[mask-image:linear-gradient(to_bottom,black_70%,transparent_100%)] max-sm:w-[60vw]"
                        aria-hidden="true"
                    >
                        <img
                            class="h-auto w-full"
                            src="{{ custom_theme_url('/vendor/auto-edit-videos/images/avatar-1.png') }}"
                            alt="{{ __('Agent Image') }}"
                            aria-hidden="true"
                            width="452"
                            height="365"
                        >
                    </div>

                    @unless ($isConfigured ?? true)
                        <x-alert variant="lg">
                            <p>{{ __('Auto Edit Videos is not configured yet. Please contact the administrator.') }}</p>
                        </x-alert>
                    @endunless

                    <div class="w-full overflow-hidden rounded-[22px] border border-transparent shadow-2xl shadow-black/[8%] dark:border-foreground/5">
                        @include('auto-edit-videos::partials.composer')
                    </div>
                </section>
            </div>

            <section
                class="w-full pt-14"
                id="my-videos"
            >
                @include('auto-edit-videos::partials.my-videos-grid', [
                    'todayEntries' => $todayEntries ?? [],
                    'previousEntries' => $previousEntries ?? [],
                ])
            </section>
        </div>
    </div>
@endsection
