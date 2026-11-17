@if (isset($planAiToolsMenu) && collect($planAiToolsMenu)->contains('key', 'ext_phone_call_agent'))
    <div class="mt-10 rounded-2xl border border-border bg-card p-6">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                {{ __('Phone Call Agent') }}
            </p>
            <p class="text-sm text-muted-foreground">
                {{ __('Usage limits and features available to subscribers on this plan.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
            <x-form.group
                label="{{ __('Seconds Limit') }}"
                tooltip="{{ __('Inbound calls. -1 for unlimited, 0 to disable, >0 for max seconds per month') }}"
                error="plan.phone_call_agent_seconds_limit"
            >
                <x-form.stepper
                    wire:model="plan.phone_call_agent_seconds_limit"
                    step="1"
                    min="-1"
                />
            </x-form.group>

            <x-form.group
                label="{{ __('Outbound Calls') }}"
                error="plan.phone_call_agent_outbound_enabled"
            >
                <x-form.checkbox
                    class="border-input rounded-input border !px-2.5 !py-3"
                    wire:model="plan.phone_call_agent_outbound_enabled"
                    label="{{ __('Enable Outbound Calls') }}"
                    tooltip="{{ __('Allow subscribers on this plan to place outbound calls and campaigns.') }}"
                    switcher
                />
            </x-form.group>

            <x-form.group
                label="{{ __('Outbound Seconds Limit') }}"
                tooltip="{{ __('Outbound calls. -1 for unlimited, 0 to disable, >0 for max seconds per month') }}"
                error="plan.phone_call_agent_outbound_seconds_limit"
            >
                <x-form.stepper
                    wire:model="plan.phone_call_agent_outbound_seconds_limit"
                    step="1"
                    min="-1"
                />
            </x-form.group>

			<x-form.group
                label="{{ __('Follow-Up Calls') }}"
                error="plan.phone_call_agent_follow_up_enabled"
            >
                <x-form.checkbox
                    class="border-input rounded-input border !px-2.5 !py-3"
                    wire:model="plan.phone_call_agent_follow_up_enabled"
                    label="{{ __('Enable Follow-Up Calls') }}"
                    tooltip="{{ __('Allow scheduling automatic follow-up / callback calls around bookings and inbound calls.') }}"
                    switcher
                />
            </x-form.group>

            <x-form.group
                label="{{ __('Outbound Calls Per Day') }}"
                tooltip="{{ __('Outbound calls. -1 for unlimited, 0 to disable, >0 for max calls per day') }}"
                error="plan.phone_call_agent_outbound_calls_per_day"
            >
                <x-form.stepper
                    wire:model="plan.phone_call_agent_outbound_calls_per_day"
                    step="1"
                    min="-1"
                />
            </x-form.group>
        </div>
    </div>
@endif
