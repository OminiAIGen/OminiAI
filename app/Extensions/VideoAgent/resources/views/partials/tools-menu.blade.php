{{--
    Replaces the old 12-tab bar. Modals open in place; the remaining tool pages
    are plain links. A route that isn't registered is skipped, same as the tab
    bar used to do.
--}}
@php
    $videoAgentToolPages = array_filter([
        'dashboard.user.video-agent.create' => ['label' => __('Create manually'), 'icon' => 'tabler-wand'],
        'dashboard.user.video-agent.speech.index' => ['label' => __('Text to Speech'), 'icon' => 'tabler-volume'],
        'dashboard.user.video-agent.tools.index' => ['label' => __('Post-production'), 'icon' => 'tabler-scissors'],
        'dashboard.user.video-agent.batches.index' => ['label' => __('Batches'), 'icon' => 'tabler-stack-2'],
        'dashboard.user.video-agent.hyperframes.index' => ['label' => __('HyperFrames'), 'icon' => 'tabler-frame'],
    ], fn ($item, $route) => Route::has($route), ARRAY_FILTER_USE_BOTH);
@endphp

<x-dropdown.dropdown
    class:dropdown="w-64 p-2"
    trigger-type="click"
    offset-y="10px"
>
    <x-slot:trigger
        class="size-9 shrink-0 border border-input-border p-0"
        variant="ghost"
        title="{{ __('More') }}"
    >
        <x-tabler-plus class="size-4 transition group-[&.lqd-is-active]/dropdown:rotate-45" />
        <span class="sr-only">{{ __('More') }}</span>
    </x-slot:trigger>

    <x-slot:dropdown>
        <ul class="m-0 flex list-none flex-col gap-0.5 p-0 [&_a]:w-full [&_button]:w-full [&_a]:items-center [&_button]:items-center [&_a]:gap-2.5 [&_button]:gap-2.5 [&_a]:rounded-dropdown [&_button]:rounded-dropdown [&_a]:px-2.5 [&_button]:px-2.5 [&_a]:py-2 [&_button]:py-2 [&_a]:text-2xs [&_button]:text-2xs [&_a:hover]:bg-foreground/5 [&_button:hover]:bg-foreground/5">
            <li>
                <button
                    class="flex"
                    type="button"
                    @click="toggle('collapse'); window.dispatchEvent(new CustomEvent('va-request-upload'))"
                >
                    <x-tabler-paperclip class="size-4 shrink-0 opacity-70" />
                    {{ __('Upload files') }}
                </button>
            </li>

            <li>
                <button
                    class="flex"
                    type="button"
                    @click="toggle('collapse'); window.dispatchEvent(new CustomEvent('va-open', { detail: 'script' }))"
                >
                    <x-tabler-sparkles class="size-4 shrink-0 opacity-70" />
                    {{ __('Script Writer') }}
                </button>
            </li>

            <li>
                <button
                    class="flex"
                    type="button"
                    @click="toggle('collapse'); window.dispatchEvent(new CustomEvent('va-open', { detail: 'brand' }))"
                >
                    <x-tabler-color-swatch class="size-4 shrink-0 opacity-70" />
                    {{ __('Brand') }}
                </button>
            </li>

            @if ($videoAgentToolPages !== [])
                <li aria-hidden="true"><hr class="my-1.5 border-dropdown-border"></li>

                @foreach ($videoAgentToolPages as $route => $item)
                    <li>
                        <a
                            class="flex"
                            href="{{ route($route) }}"
                        >
                            <x-dynamic-component
                                class="size-4 shrink-0 opacity-70"
                                :component="$item['icon']"
                            />
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </x-slot:dropdown>
</x-dropdown.dropdown>
