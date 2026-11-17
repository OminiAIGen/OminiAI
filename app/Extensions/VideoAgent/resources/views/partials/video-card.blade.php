{{-- Server-rendered video tile. Used by the tool pages that list past renders. --}}
<div class="flex flex-col overflow-hidden rounded-card border border-card-border bg-card-background">
    @if ($video->playbackUrl() && $video->kind->isAudio())
        <div class="flex items-center bg-surface p-4">
            <audio
                class="w-full"
                src="{{ $video->playbackUrl() }}"
                controls
                preload="none"
            ></audio>
        </div>
    @elseif ($video->playbackUrl())
        <video
            class="aspect-video w-full bg-black object-contain"
            src="{{ $video->playbackUrl() }}"
            poster="{{ $video->thumbnail_url }}"
            controls
            preload="metadata"
        ></video>
    @else
        <div class="flex aspect-video w-full items-center justify-center bg-surface text-2xs text-label">
            {{ $video->status->label() }}
        </div>
    @endif

    <div class="flex grow flex-col gap-1 p-4">
        <div class="truncate text-2xs font-medium">{{ $video->displayTitle() }}</div>

        <div class="truncate text-[11px] text-label">
            {{ $video->kind->label() }} &middot; {{ $video->formattedDuration() }}
        </div>

        @if ($video->error_message)
            <p class="m-0 mt-1 text-[11px] text-rose-500">{{ $video->error_message }}</p>
        @endif

        <div class="mt-auto flex flex-wrap items-center gap-3 pt-3">
            @if ($video->playbackUrl())
                <a
                    class="text-[11px] text-primary underline-offset-2 hover:underline"
                    href="{{ $video->playbackUrl() }}"
                    target="_blank"
                    rel="noopener"
                >{{ __('Download') }}</a>
            @endif

            <form
                class="ms-auto"
                method="post"
                action="{{ route('dashboard.user.video-agent.videos.destroy', $video) }}"
                onsubmit="return confirm('{{ __('Delete this video?') }}')"
            >
                @csrf
                @method('DELETE')
                <button
                    class="text-label transition hover:text-rose-500"
                    type="submit"
                >
                    <x-tabler-trash class="size-4" />
                    <span class="sr-only">{{ __('Delete') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>
