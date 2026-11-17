@if (isset($planAiToolsMenu) && collect($planAiToolsMenu)->contains('key', 'video_agent'))
    <div class="mt-10 rounded-2xl border border-border bg-card p-6">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                {{ __('Video Agent') }}
            </p>
            <p class="text-sm text-muted-foreground">
                {{ __('HeyGen Video Agent usage limits for subscribers on this plan.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
            <x-form.group
                label="{{ __('Seconds Limit') }}"
                tooltip="{{ __('Total rendered video seconds per month. -1 for unlimited, 0 to disable, >0 for a monthly cap.') }}"
                error="plan.video_agent_seconds_limit"
            >
                <x-form.stepper
                    wire:model="plan.video_agent_seconds_limit"
                    step="1"
                    min="-1"
                />
            </x-form.group>

            <x-form.group
                label="{{ __('Videos Limit') }}"
                tooltip="{{ __('Number of videos per month. -1 for unlimited, 0 to disable, >0 for a monthly cap.') }}"
                error="plan.video_agent_videos_limit"
            >
                <x-form.stepper
                    wire:model="plan.video_agent_videos_limit"
                    step="1"
                    min="-1"
                />
            </x-form.group>
        </div>
    </div>
@endif
