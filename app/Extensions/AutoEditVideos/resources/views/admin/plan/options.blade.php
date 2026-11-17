{{--
    Rendered inside the plan editor by the core step-second partial. The two
    inputs mirror the plan columns the billing service reads: -1 is unlimited
    and 0 turns the feature off for the plan.
--}}
<div class="form-control">
    <label class="form-label" for="auto_edit_videos_seconds_limit">
        {{ __('Auto Edit Videos — seconds per month') }}
    </label>
    <input
        class="border-input rounded-input border !px-2.5 !py-3"
        id="auto_edit_videos_seconds_limit"
        type="number"
        min="-1"
        wire:model="plan.auto_edit_videos_seconds_limit"
    />
    <span class="text-label mt-1 text-xs">{{ __('-1 for unlimited, 0 to disable.') }}</span>
</div>

<div class="form-control">
    <label class="form-label" for="auto_edit_videos_renders_limit">
        {{ __('Auto Edit Videos — videos per month') }}
    </label>
    <input
        class="border-input rounded-input border !px-2.5 !py-3"
        id="auto_edit_videos_renders_limit"
        type="number"
        min="-1"
        wire:model="plan.auto_edit_videos_renders_limit"
    />
    <span class="text-label mt-1 text-xs">{{ __('-1 for unlimited, 0 to disable.') }}</span>
</div>
