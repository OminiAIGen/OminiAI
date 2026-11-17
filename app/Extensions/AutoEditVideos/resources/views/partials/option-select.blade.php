@php
    $menuId = 'auto-edit-videos-' . $name . '-options';
    $selectedLabel = $options[$value] ?? reset($options);
@endphp

<x-dropdown.dropdown
    class="inline-flex"
    class:trigger="h-9 gap-2 rounded-button border border-input-border bg-background px-2.5 text-2xs font-normal text-label hover:translate-y-0 hover:border-primary hover:shadow-none focus-visible:border-primary"
    class:dropdown="max-h-[min(60vh,20rem)] w-max min-w-36 max-w-[calc(100vw-1rem)] overflow-y-auto p-1"
    triggerType="click"
    offsetY="6px"
>
    <x-slot:trigger
        variant="none"
        size="none"
        type="button"
        aria-haspopup="listbox"
        aria-controls="{{ $menuId }}"
        ::aria-expanded="open.toString()"
        @keydown.arrow-down.prevent="toggle('expand')"
        @keydown.arrow-up.prevent="toggle('expand')"
    >
        <x-dynamic-component
            class="size-3.5 shrink-0"
            :component="$icon"
            aria-hidden="true"
        />
        <span class="sr-only">{{ $label }}:</span>
        <span
            class="whitespace-nowrap"
            x-text="({{ Js::from($options) }})[String({{ $model }})] ?? ''"
        >{{ $selectedLabel }}</span>
        <x-tabler-chevron-down
            class="size-3.5 shrink-0"
            aria-hidden="true"
        />
    </x-slot:trigger>

    <x-slot:dropdown>
        <div
            id="{{ $menuId }}"
            role="listbox"
            aria-label="{{ $label }}"
            x-ref="optionList"
            x-effect="if (open) $nextTick(() => $refs.optionList.querySelector('[aria-selected=true]')?.focus())"
            @keydown.arrow-down.prevent="const options = [...$el.querySelectorAll('[role=option]')]; options[(options.indexOf(document.activeElement) + 1) % options.length]?.focus()"
            @keydown.arrow-up.prevent="const options = [...$el.querySelectorAll('[role=option]')]; const index = options.indexOf(document.activeElement); options[index < 0 ? options.length - 1 : (index - 1 + options.length) % options.length]?.focus()"
            @keydown.home.prevent="$el.querySelector('[role=option]')?.focus()"
            @keydown.end.prevent="$el.querySelector('[role=option]:last-child')?.focus()"
            @keydown.escape.prevent.stop="toggle('collapse'); $refs.trigger.focus()"
            @keydown.tab.prevent="toggle('collapse'); $refs.trigger.focus()"
        >
            @foreach ($options as $optionValue => $optionLabel)
                <button
                    class="flex w-full items-center gap-2 rounded-dropdown px-3 py-2 text-start text-2xs text-dropdown-foreground transition hover:bg-foreground/5 focus-visible:bg-primary/10 focus-visible:outline-none"
                    type="button"
                    role="option"
                    tabindex="-1"
                    :aria-selected="String({{ $model }}) === {{ Js::from((string) $optionValue) }}"
                    :class="{ 'bg-primary/10 text-primary': String({{ $model }}) === {{ Js::from((string) $optionValue) }} }"
                    @click="{{ $model }} = {{ Js::from((string) $optionValue) }}; toggle('collapse'); $refs.trigger.focus()"
                >
                    <span class="whitespace-nowrap">{{ $optionLabel }}</span>
                    <x-tabler-check
                        class="ms-auto size-3.5 shrink-0"
                        x-show="String({{ $model }}) === {{ Js::from((string) $optionValue) }}"
                        aria-hidden="true"
                    />
                </button>
            @endforeach
        </div>
    </x-slot:dropdown>
</x-dropdown.dropdown>

<input
    type="hidden"
    name="{{ $name }}"
    value="{{ $value }}"
    x-model="{{ $model }}"
>
