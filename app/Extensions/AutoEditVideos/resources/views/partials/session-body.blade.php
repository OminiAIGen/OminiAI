<x-card
    class="lqd-auto-edit-thread"
    size="none"
>
    <x-slot:head>
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-sm font-medium">{{ __('Conversation') }}</h3>
            <x-badge variant="{{ $session->status->badgeVariant() }}">
                {{ $session->status->label() }}
            </x-badge>
        </div>
    </x-slot:head>

    <div class="px-6 py-5">
        @if ($session->error_message)
            <x-alert
                class="mb-4"
                variant="lg"
                type="danger"
            >
                <p>{{ $session->error_message }}</p>
            </x-alert>
        @endif

        <div class="flex flex-col gap-3.5">
            @foreach ($session->messages as $message)
                <div @class(['flex flex-col gap-1', 'items-end' => $message->isFromUser()])>
                    <span class="text-label text-4xs">
                        {{ $message->isFromUser() ? __('You') : __('Editor') }}
                    </span>

                    {{--
                        `whitespace-pre-line` keeps newlines so a multi-line brief
                        reads as written, which also means the tags have to close
                        tight around the body — Blade's own indentation would
                        otherwise render as blank lines above and below the text.
                    --}}
                    <div
                        @class([
                            'max-w-[85%] whitespace-pre-line rounded-card px-3.5 py-2.5 text-2xs leading-relaxed',
                            'bg-primary text-primary-foreground' => $message->isFromUser(),
                            'bg-foreground/5' => !$message->isFromUser(),
                        ])
                    >{{ trim($message->body) }}</div>
                </div>
            @endforeach

            @if ($session->status->isAwaitingAgent())
                <div class="flex flex-col gap-1">
                    <span class="text-label text-4xs">{{ __('Editor') }}</span>
                    <div class="bg-foreground/5 rounded-card px-3.5 py-2.5">
                        <x-shimmer-text class="text-2xs">{{ __('Planning the edit…') }}</x-shimmer-text>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($session->renders->isNotEmpty())
        <div class="border-card-border border-t px-6 py-5">
            <h4 class="text-label mb-3 text-4xs uppercase tracking-wide">{{ __('Renders') }}</h4>

            <div class="flex flex-col gap-3">
                @foreach ($session->renders as $render)
                    @include('auto-edit-videos::partials.render-card', ['render' => $render])
                @endforeach
            </div>
        </div>
    @endif
</x-card>
