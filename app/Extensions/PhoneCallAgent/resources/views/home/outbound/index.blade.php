@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Outbound Calls'))
@section('titlebar_subtitle')
    {{ __('Place outbound calls and run batch calls from your phone agents.') }}
@endsection
@section('titlebar_actions')
    <x-button
        href="{{ route('dashboard.phone-call-agent.index') }}"
        variant="ghost-shadow"
    >
        <x-tabler-arrow-left class="size-4" />
        @lang('Back to Agents')
    </x-button>
@endsection

@section('content')
    <div
        class="py-10"
        x-data="phoneCallAgentOutbound"
    >
        <template x-if="!agents.length">
            <x-card class="mx-auto max-w-xl text-center">
                <x-tabler-phone-off class="mx-auto mb-4 size-10 opacity-40" />
                <h3 class="mb-2">@lang('No phone numbers yet')</h3>
                <p class="mb-5 text-sm opacity-60">@lang('Attach a phone number to one of your agents before placing outbound calls.')</p>
                <x-button href="{{ route('dashboard.phone-call-agent.index') }}">
                    @lang('Go to Agents')
                </x-button>
            </x-card>
        </template>

        <template x-if="agents.length">
            <div class="flex flex-col gap-6">
                {{-- Actions --}}
                @unless ($app_is_demo)
                    <div class="flex flex-wrap gap-3">
                        {{-- Place a call --}}
                        <x-modal id="place-call-modal">
                            <x-slot:title>@lang('Place a Call')</x-slot:title>

                            <x-slot:trigger type="button">
                                <x-tabler-phone-outgoing class="size-4" />
                                @lang('Place a Call')
                            </x-slot:trigger>

                            <x-slot:modal>
                                <div
                                    class="flex flex-col gap-4"
                                    x-data="{ s: $store.phoneCallAgentOutbound }"
                                >
                                    <p class="text-2xs opacity-60">@lang('Call a single number now, or schedule it for later.')</p>

                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('From Agent') }}"
                                        type="select"
                                        name="agent"
                                        x-model="s.selectedAgent"
                                    >
                                        <template
                                            x-for="agent in s.agents"
                                            :key="agent.uuid"
                                        >
                                            <option
                                                :value="agent.uuid"
                                                x-text="`${agent.title} — ${agent.phone_number}`"
                                            ></option>
                                        </template>
                                    </x-forms.input>

                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Phone Number to Call') }}"
                                        type="text"
                                        name="to_number"
                                        x-model="s.callForm.to_number"
                                        placeholder="+14155552671"
                                    />

                                    <div>
                                        <x-forms.input
                                            class:label="text-heading-foreground"
                                            label="{{ __('Schedule (optional)') }}"
                                            type="datetime-local"
                                            name="scheduled_at"
                                            x-model="s.callForm.scheduled_at"
                                        />
                                        <p
                                            class="mt-1 text-2xs text-label"
                                            x-data="{ now: '' }"
                                            x-init="now = new Date().toLocaleString(); setInterval(() => now = new Date().toLocaleString(), 1000)"
                                        >
                                            @lang('Current time'): <span x-text="now"></span>
                                        </p>
                                    </div>

                                    <x-button
                                        class="w-full"
                                        type="button"
                                        ::disabled="s.busy"
                                        @click="s.placeCall()"
                                    >
                                        <x-tabler-phone-outgoing class="size-4" />
                                        <span x-text="s.callForm.scheduled_at ? '{{ __('Schedule Call') }}' : '{{ __('Call Now') }}'"></span>
                                    </x-button>
                                </div>
                            </x-slot:modal>
                        </x-modal>

                        {{-- New campaign --}}
                        <x-modal id="new-campaign-modal">
                            <x-slot:title>@lang('New Batch Call')</x-slot:title>

                            <x-slot:trigger variant="outline" type="button">
                                <x-tabler-users class="size-4" />
                                @lang('New Batch Call')
                            </x-slot:trigger>

                            <x-slot:modal>
                                <div
                                    class="flex flex-col gap-4"
                                    x-data="{ s: $store.phoneCallAgentOutbound }"
                                >
                                    <p class="text-2xs opacity-60">@lang('Call many numbers automatically, spread across days to respect your plan limits.')</p>

                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('From Agent') }}"
                                        type="select"
                                        name="campaign_agent"
                                        x-model="s.selectedAgent"
                                    >
                                        <template
                                            x-for="agent in s.agents"
                                            :key="agent.uuid"
                                        >
                                            <option
                                                :value="agent.uuid"
                                                x-text="`${agent.title} — ${agent.phone_number}`"
                                            ></option>
                                        </template>
                                    </x-forms.input>

                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Batch Call Name') }}"
                                        type="text"
                                        name="campaign_name"
                                        x-model="s.campaignForm.name"
                                    />

                                    <div>
                                        <x-forms.input
                                            class="min-h-32 font-mono text-xs"
                                            class:label="text-heading-foreground"
                                            label="{{ __('Phone Numbers') }}"
                                            type="textarea"
                                            name="campaign_numbers"
                                            x-model="s.campaignForm.numbers"
                                            placeholder="+14155552671&#10;+14155559988"
                                        />
                                        <p class="mt-1 text-2xs opacity-60">@lang('One number per line (or comma-separated).')</p>
                                    </div>

                                    <div>
                                        <x-forms.input
                                            class:label="text-heading-foreground"
                                            label="{{ __('Schedule (optional)') }}"
                                            type="datetime-local"
                                            name="campaign_scheduled_at"
                                            x-model="s.campaignForm.scheduled_at"
                                        />
                                        <p
                                            class="mt-1 text-2xs text-label"
                                            x-data="{ now: '' }"
                                            x-init="now = new Date().toLocaleString(); setInterval(() => now = new Date().toLocaleString(), 1000)"
                                        >
                                            @lang('Current time'): <span x-text="now"></span>
                                        </p>
                                    </div>

                                    <x-button
                                        class="w-full"
                                        type="button"
                                        ::disabled="s.busy"
                                        @click="s.startCampaign()"
                                    >
                                        <x-tabler-send class="size-4" />
                                        @lang('Create Batch Call')
                                    </x-button>
                                </div>
                            </x-slot:modal>
                        </x-modal>
                    </div>
                @endunless

                {{-- Campaigns list --}}
                <x-card>
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="mb-0">@lang('Batch Calls')</h3>
                        <x-button
                            class="text-2xs"
                            variant="ghost"
                            size="sm"
                            type="button"
                            @click="loadCampaigns()"
                        >
                            <x-tabler-refresh class="size-4" />
                            @lang('Refresh')
                        </x-button>
                    </div>

                    <template x-if="!campaigns.length">
                        <p class="text-sm opacity-60">@lang('No batch calls yet.')</p>
                    </template>

                    <div class="flex flex-col gap-3">
                        <template
                            x-for="campaign in campaigns"
                            :key="campaign.uuid"
                        >
                            <div class="flex flex-col gap-2 rounded-lg border px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="grow truncate text-sm font-medium"
                                        x-text="campaign.name"
                                    ></span>
                                    <span
                                        class="rounded px-1.5 py-0.5 text-[10px] font-medium capitalize"
                                        :class="campaign.status === 'completed' ? 'bg-green-500/15 text-green-600' : (campaign.status === 'cancelled' ? 'bg-red-500/15 text-red-600' : 'bg-yellow-500/15 text-yellow-600')"
                                        x-text="campaign.status"
                                    ></span>
                                    <x-button
                                        class="text-2xs"
                                        variant="ghost"
                                        size="sm"
                                        type="button"
                                        @click="openCampaignDetails(campaign.uuid)"
                                    >
                                        <x-tabler-list-details class="size-3.5" />
                                        @lang('phone-call-agent::phone-call-agent.view_details')
                                    </x-button>
                                    <template x-if="campaign.status !== 'completed' && campaign.status !== 'cancelled'">
                                        <div class="flex items-center gap-2">
                                            <x-button
                                                class="text-2xs"
                                                variant="outline"
                                                size="sm"
                                                type="button"
                                                @click="openEditCampaign(campaign.uuid)"
                                            >
                                                <x-tabler-pencil class="size-3.5" />
                                                @lang('Edit')
                                            </x-button>
                                            <x-button
                                                class="text-2xs"
                                                variant="outline"
                                                size="sm"
                                                type="button"
                                                @click="cancelCampaign(campaign.uuid)"
                                            >
                                                @lang('Cancel')
                                            </x-button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Progress --}}
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-heading-foreground/10">
                                    <div
                                        class="h-full rounded-full bg-primary transition-all"
                                        :style="`width: ${campaign.total_recipients ? Math.round(((campaign.completed_count + campaign.failed_count) / campaign.total_recipients) * 100) : 0}%`"
                                    ></div>
                                </div>

                                <p class="mb-0 text-2xs opacity-60">
                                    <span x-text="campaign.completed_count + campaign.failed_count"></span>/<span x-text="campaign.total_recipients"></span>
                                    @lang('placed') ·
                                    <span x-text="campaign.failed_count"></span> @lang('failed')
                                </p>
                            </div>
                        </template>
                    </div>
                </x-card>

                {{-- Outbound calls table --}}
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="mb-0">@lang('Outbound Calls')</h3>
                        <div class="flex flex-wrap items-center gap-2">
                            <x-forms.input
                                class="!h-9 !w-auto min-w-[140px] !text-xs pe-8"
                                type="select"
                                name="calls_agent"
                                x-model="callsAgent"
                                @change="loadCalls(1)"
                            >
                                <option value="all">@lang('All Agents')</option>
                                <template
                                    x-for="agent in agents"
                                    :key="agent.uuid"
                                >
                                    <option
                                        :value="agent.uuid"
                                        x-text="agent.title"
                                    ></option>
                                </template>
                            </x-forms.input>

                            <div class="relative">
                                <x-tabler-search class="pointer-events-none absolute inset-y-0 start-2.5 my-auto size-4 opacity-40" />
                                <x-forms.input
                                    class="!h-9 !w-auto min-w-[160px] !text-xs !ps-8"
                                    type="text"
                                    name="calls_to"
                                    x-model="callsTo"
                                    @input.debounce.400ms="loadCalls(1)"
                                    placeholder="{{ __('Search number') }}"
                                />
                            </div>

                            <x-forms.input
                                class="!h-9 !w-auto min-w-[140px] !text-xs pe-8"
                                type="select"
                                name="calls_status"
                                x-model="callsStatus"
                                @change="loadCalls(1)"
                            >
                                <option value="all">@lang('All Statuses')</option>
                                <option value="pending">@lang('Scheduled')</option>
                                <option value="queued">@lang('In Progress')</option>
                                <option value="completed">@lang('Completed')</option>
                                <option value="failed">@lang('Failed')</option>
                                <option value="missed">@lang('Missed')</option>
                                <option value="cancelled">@lang('Cancelled')</option>
                            </x-forms.input>
                            <x-button
                                variant="ghost-shadow"
                                size="none"
                                class="size-9"
                                type="button"
                                @click="loadCalls(callsPage)"
                            >
                                <x-tabler-refresh class="size-4" />
                            </x-button>
                        </div>
                    </div>

                    <x-table>
                        <x-slot:head>
                            <tr>
                                <th>@lang('Agent')</th>
                                <th>@lang('To')</th>
                                <th>
                                    <button
                                        class="inline-flex items-center gap-1 transition hover:text-heading-foreground"
                                        type="button"
                                        @click="sortBy('status')"
                                    >
                                        @lang('Status')
                                        <span x-html="sortIcon('status')"></span>
                                    </button>
                                </th>
                                <th>
                                    <button
                                        class="inline-flex items-center gap-1 transition hover:text-heading-foreground"
                                        type="button"
                                        @click="sortBy('when')"
                                    >
                                        @lang('When')
                                        <span x-html="sortIcon('when')"></span>
                                    </button>
                                </th>
                                <th>
                                    <button
                                        class="inline-flex items-center gap-1 transition hover:text-heading-foreground"
                                        type="button"
                                        @click="sortBy('date')"
                                    >
                                        @lang('Date & Time')
                                        <span x-html="sortIcon('date')"></span>
                                    </button>
                                </th>
                                <th class="text-end">
                                    <button
                                        class="inline-flex items-center gap-1 transition hover:text-heading-foreground"
                                        type="button"
                                        @click="sortBy('duration')"
                                    >
                                        @lang('Duration')
                                        <span x-html="sortIcon('duration')"></span>
                                    </button>
                                </th>
                                <th class="text-end">@lang('Actions')</th>
                            </tr>
                        </x-slot:head>

                        <x-slot:body>
                            <template
                                x-for="call in calls"
                                :key="call.uuid"
                            >
                                <tr>
                                    <td x-text="call.agent_title || '—'"></td>
                                    <td
                                        class="font-mono text-xs"
                                        x-text="call.to_number || '—'"
                                    ></td>
                                    <td>
                                        <span
                                            class="inline-block rounded px-1.5 py-0.5 text-[10px] font-medium capitalize"
                                            :class="statusClass(call)"
                                            :title="isWaiting(call) ? waitingReason(call) : (call.status_reason || '')"
                                            x-text="statusLabel(call)"
                                        ></span>
                                    </td>
                                    <td
                                        class="text-xs text-label"
                                        x-text="whenLabel(call)"
                                    ></td>
                                    <td
                                        class="whitespace-nowrap text-xs text-label"
                                        x-text="fmtDate(call.created_at)"
                                    ></td>
                                    <td
                                        class="text-end font-mono text-xs"
                                        x-text="call.duration ? formatDuration(call.duration) : '—'"
                                    ></td>
                                    <td class="text-end">
                                        <div class="flex justify-end">
                                            <template x-if="call.status !== 'cancelled'">
                                                <x-dropdown.dropdown
                                                    anchor="end"
                                                    triggerType="click"
                                                    offsetY="8px"
                                                    :teleport="true"
                                                >
                                                    <x-slot:trigger
                                                        class="size-9 shrink-0 p-0"
                                                        variant="ghost-shadow"
                                                        size="none"
                                                        title="{{ __('Actions') }}"
                                                    >
                                                        <x-tabler-dots-vertical class="size-4" />
                                                    </x-slot:trigger>

                                                    <x-slot:dropdown class="min-w-[190px] overflow-hidden rounded-lg bg-background py-1 shadow-lg">
                                                        <template x-if="canViewLogs(call.status)">
                                                            <x-button
                                                                class="w-full justify-start rounded-none text-start hover:transform-none"
                                                                variant="ghost"
                                                                type="button"
                                                                @click.prevent="viewLogs(call.uuid)"
                                                            >
                                                                <x-tabler-message-2 class="size-4" />
                                                                @lang('phone-call-agent::phone-call-agent.view_logs')
                                                            </x-button>
                                                        </template>

                                                        <template x-if="call.status === 'completed'">
                                                            <x-button
                                                                class="w-full justify-start rounded-none text-start hover:transform-none"
                                                                variant="ghost"
                                                                ::href="historyUrl(call.uuid)"
                                                            >
                                                                <x-tabler-external-link class="size-4" />
                                                                @lang('View in Call History')
                                                            </x-button>
                                                        </template>

                                                        <template x-if="call.status === 'pending'">
                                                            <x-button
                                                                class="w-full justify-start rounded-none text-start hover:transform-none"
                                                                variant="ghost"
                                                                type="button"
                                                                @click.prevent="openReschedule(call)"
                                                            >
                                                                <x-tabler-calendar-clock class="size-4" />
                                                                @lang('phone-call-agent::phone-call-agent.reschedule')
                                                            </x-button>
                                                        </template>

                                                        <template x-if="call.status === 'pending'">
                                                            <x-button
                                                                class="w-full justify-start rounded-none text-start text-red-600 hover:transform-none"
                                                                variant="ghost"
                                                                type="button"
                                                                @click.prevent="cancelCall(call.uuid)"
                                                            >
                                                                <x-tabler-x class="size-4" />
                                                                @lang('Cancel')
                                                            </x-button>
                                                        </template>
                                                    </x-slot:dropdown>
                                                </x-dropdown.dropdown>
                                            </template>

                                            <template x-if="call.status === 'cancelled'">
                                                <span class="text-end font-mono text-xs">—</span>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!calls.length && !callsLoading">
                                <tr>
                                    <td
                                        class="py-6 text-center text-sm text-label"
                                        colspan="7"
                                    >@lang('No outbound calls yet.')</td>
                                </tr>
                            </template>
                        </x-slot:body>
                    </x-table>

                    {{-- Pagination --}}
                    <div
                        class="flex items-center justify-between"
                        x-show="callsLastPage > 1"
                        x-cloak
                    >
                        <x-button
                            variant="outline"
                            size="sm"
                            type="button"
                            ::disabled="callsPage <= 1 || callsLoading"
                            @click="loadCalls(callsPage - 1)"
                        >
                            <x-tabler-chevron-left class="size-4" />
                            @lang('Previous')
                        </x-button>
                        <span class="text-xs text-label">
                            <span x-text="callsPage"></span> / <span x-text="callsLastPage"></span>
                        </span>
                        <x-button
                            variant="outline"
                            size="sm"
                            type="button"
                            ::disabled="callsPage >= callsLastPage || callsLoading"
                            @click="loadCalls(callsPage + 1)"
                        >
                            @lang('Next')
                            <x-tabler-chevron-right class="size-4" />
                        </x-button>
                    </div>
                </div>

                {{-- Call logs modal (opened from the row Actions dropdown) --}}
                <x-modal id="call-logs-modal">
                    <x-slot:title>@lang('phone-call-agent::phone-call-agent.call_logs')</x-slot:title>

                    <x-slot:modal>
                        <div x-data="{ s: $store.phoneCallAgentOutbound }">
                            {{-- Loading --}}
                            <template x-if="s.logsLoading">
                                <div class="flex justify-center py-10">
                                    <x-tabler-loader-2 class="size-6 animate-spin opacity-50" />
                                </div>
                            </template>

                            <template x-if="!s.logsLoading && s.logsCall">
                                <div class="flex flex-col gap-4">
                                    {{-- Failure reason banner --}}
                                    <template x-if="s.logsCall.status === 'failed'">
                                        <div class="flex items-start gap-2 rounded-lg bg-red-500/10 px-3 py-2.5 text-xs text-red-600">
                                            <x-tabler-alert-triangle class="mt-px size-4 shrink-0" />
                                            <span x-text="s.logsCall.status_reason || '{{ __('phone-call-agent::phone-call-agent.outbound_failed_no_reason') }}'"></span>
                                        </div>
                                    </template>

                                    {{-- Overdue-but-pending banner: the row is late, not failed --}}
                                    <template x-if="s.isWaiting(s.logsCall)">
                                        <div class="flex items-start gap-2 rounded-lg bg-amber-500/10 px-3 py-2.5 text-xs text-amber-600">
                                            <x-tabler-clock-exclamation class="mt-px size-4 shrink-0" />
                                            <span x-text="s.waitingReason(s.logsCall)"></span>
                                        </div>
                                    </template>

                                    {{-- API record --}}
                                    <dl class="divide-y text-sm">
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-label">@lang('Status')</dt>
                                            <dd>
                                                <span
                                                    class="inline-block rounded px-1.5 py-0.5 text-[10px] font-medium capitalize"
                                                    :class="s.statusClass(s.logsCall)"
                                                    x-text="s.statusLabel(s.logsCall)"
                                                ></span>
                                            </dd>
                                        </div>
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-label">@lang('Provider')</dt>
                                            <dd class="capitalize" x-text="s.logsCall.provider || '—'"></dd>
                                        </div>
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-label">@lang('Provider Call ID')</dt>
                                            <dd
                                                class="max-w-[60%] truncate font-mono text-xs"
                                                x-text="s.logsCall.call_sid || '—'"
                                                :title="s.logsCall.call_sid || ''"
                                            ></dd>
                                        </div>
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-label">@lang('From')</dt>
                                            <dd class="font-mono text-xs" x-text="s.logsCall.from_number || '—'"></dd>
                                        </div>
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-label">@lang('To')</dt>
                                            <dd class="font-mono text-xs" x-text="s.logsCall.to_number || '—'"></dd>
                                        </div>
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-label">@lang('Duration')</dt>
                                            <dd
                                                class="font-mono text-xs"
                                                x-text="s.logsCall.duration ? s.formatDuration(s.logsCall.duration) : '—'"
                                            ></dd>
                                        </div>
                                        <template x-if="s.logsCall.scheduled_at">
                                            <div class="flex items-center justify-between gap-4 py-2">
                                                <dt class="text-label">@lang('Scheduled')</dt>
                                                <dd class="text-xs" x-text="s.fmtDate(s.logsCall.scheduled_at)"></dd>
                                            </div>
                                        </template>
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-label">@lang('Started')</dt>
                                            <dd class="text-xs" x-text="s.fmtDate(s.logsCall.started_at)"></dd>
                                        </div>
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-label">@lang('Ended')</dt>
                                            <dd class="text-xs" x-text="s.fmtDate(s.logsCall.ended_at)"></dd>
                                        </div>
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-label">@lang('Created')</dt>
                                            <dd class="text-xs" x-text="s.fmtDate(s.logsCall.created_at)"></dd>
                                        </div>
                                    </dl>
                                </div>
                            </template>
                        </div>
                    </x-slot:modal>
                </x-modal>

                {{-- Reschedule call modal (opened from the row Actions dropdown) --}}
                <x-modal id="reschedule-call-modal">
                    <x-slot:title>@lang('phone-call-agent::phone-call-agent.reschedule')</x-slot:title>

                    <x-slot:modal>
                        <div
                            class="flex flex-col gap-4"
                            x-data="{ s: $store.phoneCallAgentOutbound }"
                        >
                            <p class="text-2xs opacity-60">@lang('Pick a new date and time for this scheduled call.')</p>

                            <div>
                                <x-forms.input
                                    class:label="text-heading-foreground"
                                    label="{{ __('New Schedule') }}"
                                    type="datetime-local"
                                    name="reschedule_at"
                                    x-model="s.rescheduleForm.scheduled_at"
                                />
                                <p
                                    class="mt-1 text-2xs text-label"
                                    x-data="{ now: '' }"
                                    x-init="now = new Date().toLocaleString(); setInterval(() => now = new Date().toLocaleString(), 1000)"
                                >
                                    @lang('Current time'): <span x-text="now"></span>
                                </p>
                            </div>

                            <x-button
                                class="w-full"
                                type="button"
                                ::disabled="s.busy"
                                @click="s.submitReschedule()"
                            >
                                <x-tabler-calendar-clock class="size-4" />
                                @lang('Save')
                            </x-button>
                        </div>
                    </x-slot:modal>
                </x-modal>

                {{-- Edit campaign modal (opened from a campaign card) --}}
                <x-modal id="edit-campaign-modal">
                    <x-slot:title>@lang('phone-call-agent::phone-call-agent.edit_campaign')</x-slot:title>

                    <x-slot:modal>
                        <div
                            class="flex flex-col gap-4"
                            x-data="{ s: $store.phoneCallAgentOutbound }"
                        >
                            <template x-if="s.editCampaignLoading">
                                <div class="flex justify-center py-10">
                                    <x-tabler-loader-2 class="size-6 animate-spin opacity-50" />
                                </div>
                            </template>

                            <template x-if="!s.editCampaignLoading">
                                <div class="flex flex-col gap-4">
                                    <p class="text-2xs opacity-60">@lang('Update the name, recipient list, and schedule. Already-placed calls are kept.')</p>

                                    <x-forms.input
                                        class:label="text-heading-foreground"
                                        label="{{ __('Batch Call Name') }}"
                                        type="text"
                                        name="edit_campaign_name"
                                        x-model="s.editCampaignForm.name"
                                    />

                                    <div>
                                        <x-forms.input
                                            class="min-h-32 font-mono text-xs"
                                            class:label="text-heading-foreground"
                                            label="{{ __('Phone Numbers') }}"
                                            type="textarea"
                                            name="edit_campaign_numbers"
                                            x-model="s.editCampaignForm.numbers"
                                            placeholder="+14155552671&#10;+14155559988"
                                        />
                                        <p class="mt-1 text-2xs opacity-60">@lang('One number per line (or comma-separated). Only not-yet-placed numbers can be changed.')</p>
                                    </div>

                                    <div>
                                        <x-forms.input
                                            class:label="text-heading-foreground"
                                            label="{{ __('Schedule (optional)') }}"
                                            type="datetime-local"
                                            name="edit_campaign_scheduled_at"
                                            x-model="s.editCampaignForm.scheduled_at"
                                        />
                                        <p
                                            class="mt-1 text-2xs text-label"
                                            x-data="{ now: '' }"
                                            x-init="now = new Date().toLocaleString(); setInterval(() => now = new Date().toLocaleString(), 1000)"
                                        >
                                            @lang('Current time'): <span x-text="now"></span>
                                        </p>
                                    </div>

                                    <x-button
                                        class="w-full"
                                        type="button"
                                        ::disabled="s.busy"
                                        @click="s.submitEditCampaign()"
                                    >
                                        <x-tabler-device-floppy class="size-4" />
                                        @lang('Save')
                                    </x-button>
                                </div>
                            </template>
                        </div>
                    </x-slot:modal>
                </x-modal>

                {{-- Campaign details modal (opened from a campaign card) --}}
                <x-modal id="campaign-details-modal">
                    <x-slot:title>@lang('phone-call-agent::phone-call-agent.campaign_details')</x-slot:title>

                    <x-slot:modal>
                        <div x-data="{ s: $store.phoneCallAgentOutbound }">
                            <template x-if="s.detailsLoading">
                                <div class="flex justify-center py-10">
                                    <x-tabler-loader-2 class="size-6 animate-spin opacity-50" />
                                </div>
                            </template>

                            <template x-if="!s.detailsLoading && s.detailsCampaign">
                                <div class="flex flex-col gap-4">
                                    {{-- Header --}}
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p
                                                class="truncate text-sm font-semibold"
                                                x-text="s.detailsCampaign.name"
                                            ></p>
                                            <p
                                                class="text-2xs text-label"
                                                x-text="s.detailsCampaign.agent_title || '—'"
                                            ></p>
                                        </div>
                                        <span
                                            class="shrink-0 rounded px-1.5 py-0.5 text-[10px] font-medium capitalize"
                                            :class="s.detailsCampaign.status === 'completed' ? 'bg-green-500/15 text-green-600' : (s.detailsCampaign.status === 'cancelled' ? 'bg-red-500/15 text-red-600' : 'bg-yellow-500/15 text-yellow-600')"
                                            x-text="s.detailsCampaign.status"
                                        ></span>
                                    </div>

                                    {{-- Stats --}}
                                    <div class="grid grid-cols-3 gap-2 text-center">
                                        <div class="rounded-lg border px-2 py-3">
                                            <p class="text-lg font-semibold" x-text="s.detailsCampaign.total_recipients"></p>
                                            <p class="text-2xs text-label">@lang('Recipients')</p>
                                        </div>
                                        <div class="rounded-lg border px-2 py-3">
                                            <p class="text-lg font-semibold text-green-600" x-text="s.detailsCampaign.completed_count"></p>
                                            <p class="text-2xs text-label">@lang('Completed')</p>
                                        </div>
                                        <div class="rounded-lg border px-2 py-3">
                                            <p class="text-lg font-semibold text-red-600" x-text="s.detailsCampaign.failed_count"></p>
                                            <p class="text-2xs text-label">@lang('Failed')</p>
                                        </div>
                                    </div>

                                    <template x-if="s.detailsCampaign.scheduled_at">
                                        <p class="text-2xs text-label">
                                            @lang('Scheduled'): <span x-text="s.fmtDate(s.detailsCampaign.scheduled_at)"></span>
                                        </p>
                                    </template>

                                    {{-- Recipients --}}
                                    <div class="flex flex-col gap-1">
                                        <p class="text-2xs font-medium text-label">@lang('Recipients')</p>
                                        <div class="max-h-64 overflow-y-auto rounded-lg border">
                                            <template
                                                x-for="(call, i) in s.detailsCampaign.calls"
                                                :key="call.uuid"
                                            >
                                                <div
                                                    class="flex items-center justify-between gap-3 px-3 py-2"
                                                    :class="i > 0 ? 'border-t' : ''"
                                                >
                                                    <span
                                                        class="truncate font-mono text-xs"
                                                        x-text="call.to_number"
                                                    ></span>
                                                    <div class="flex shrink-0 items-center gap-2">
                                                        <span
                                                            class="font-mono text-2xs text-label"
                                                            x-text="call.duration ? s.formatDuration(call.duration) : ''"
                                                        ></span>
                                                        <span
                                                            class="inline-block rounded px-1.5 py-0.5 text-[10px] font-medium capitalize"
                                                            :class="s.statusClass(call)"
                                                            :title="s.isWaiting(call) ? s.waitingReason(call) : (call.status_reason || '')"
                                                            x-text="s.statusLabel(call)"
                                                        ></span>
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="!s.detailsCampaign.calls.length">
                                                <p class="px-3 py-4 text-center text-xs text-label">@lang('No recipients.')</p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </x-slot:modal>
                </x-modal>
            </div>
        </template>
    </div>
@endsection

@push('script')
    <script>
        (() => {
            document.addEventListener('alpine:init', () => {
                Alpine.data('phoneCallAgentOutbound', () => ({
                    agents: @json($agents),
                    selectedAgent: @json($agents[0]['uuid'] ?? ''),
                    busy: false,
                    callForm: { to_number: '', scheduled_at: '' },
                    campaignForm: { name: '', numbers: '', scheduled_at: '' },
                    campaigns: [],
                    calls: [],
                    callsPage: 1,
                    callsLastPage: 1,
                    callsStatus: 'all',
                    callsAgent: 'all',
                    callsTo: '',
                    callsSort: 'date',
                    callsDir: 'desc',
                    callsLoading: false,
                    logsCall: null,
                    logsLoading: false,
                    rescheduleForm: { uuid: '', scheduled_at: '' },
                    editCampaignForm: { uuid: '', name: '', numbers: '', scheduled_at: '' },
                    editCampaignLoading: false,
                    detailsCampaign: null,
                    detailsLoading: false,

                    init() {
                        Alpine.store('phoneCallAgentOutbound', this);
                        this.loadCampaigns();
                        this.loadCalls();
                    },

                    closeModal(id) {
                        const el = document.querySelector(id);
                        if (el) { Alpine.$data(el).modalOpen = false; }
                    },

                    async loadCalls(page = 1) {
                        if (this.callsLoading) { return; }
                        this.callsLoading = true;
                        try {
                            const params = new URLSearchParams({
                                page,
                                status: this.callsStatus,
                                agent: this.callsAgent,
                                to: this.callsTo,
                                sort: this.callsSort,
                                dir: this.callsDir,
                            });
                            const url = `{{ route('dashboard.phone-call-agent.outbound.calls') }}?${params.toString()}`;
                            const res = await fetch(url, {
                                method: 'GET',
                                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            });
                            const data = await res.json();
                            if (!res.ok) { this.calls = []; return; }
                            this.calls = data.data || [];
                            this.callsPage = data.current_page || 1;
                            this.callsLastPage = data.last_page || 1;
                        } catch (e) {
                            this.calls = [];
                        } finally {
                            this.callsLoading = false;
                        }
                    },

                    async cancelCall(uuid) {
                        @if ($app_is_demo)
                            toastr.error('{{ __('phone-call-agent::phone-call-agent.demo_mode_disabled') }}');
                            return;
                        @endif

                        if (!confirm('{{ __('Cancel this scheduled call?') }}')) { return; }

                        const res = await fetch(`/dashboard/phone-call-agent/outbound/calls/${uuid}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                        });
                        const data = await res.json();

                        if (!res.ok) {
                            toastr.error(data.message || 'Error');
                            return;
                        }

                        toastr.success(data.message);
                        this.loadCalls(this.callsPage);
                    },

                    openReschedule(call) {
                        this.rescheduleForm = {
                            uuid: call.uuid,
                            scheduled_at: this.toLocalInput(call.scheduled_at),
                        };
                        const modal = document.querySelector('#reschedule-call-modal');
                        if (modal) { Alpine.$data(modal).modalOpen = true; }
                    },

                    async submitReschedule() {
                        @if ($app_is_demo)
                            toastr.error('{{ __('phone-call-agent::phone-call-agent.demo_mode_disabled') }}');
                            return;
                        @endif

                        if (!this.rescheduleForm.scheduled_at) { toastr.warning('{{ __('New Schedule') }}'); return; }
                        if (this.busy) { return; }
                        this.busy = true;

                        try {
                            const res = await fetch(`/dashboard/phone-call-agent/outbound/calls/${this.rescheduleForm.uuid}/reschedule`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    scheduled_at: this.toIsoInstant(this.rescheduleForm.scheduled_at),
                                }),
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                toastr.error(data.message || Object.values(data.errors || {}).flat()[0] || 'Error');
                                return;
                            }

                            toastr.success(data.message);
                            this.closeModal('#reschedule-call-modal');
                            this.loadCalls(this.callsPage);
                        } catch (e) {
                            toastr.error('Error');
                        } finally {
                            this.busy = false;
                        }
                    },

                    historyUrl(uuid) {
                        return '{{ route('dashboard.phone-call-agent.history') }}?call=' + uuid;
                    },

                    sortBy(column) {
                        if (this.callsSort === column) {
                            this.callsDir = this.callsDir === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.callsSort = column;
                            this.callsDir = 'desc';
                        }
                        this.loadCalls(1);
                    },

                    sortIcon(column) {
                        if (this.callsSort !== column) {
                            return '<svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 opacity-30" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 9l4 -4l4 4"/><path d="M16 15l-4 4l-4 -4"/></svg>';
                        }
                        const path = this.callsDir === 'asc' ? '<path d="M6 15l6 -6l6 6"/>' : '<path d="M6 9l6 6l6 -6"/>';
                        return '<svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>' + path + '</svg>';
                    },

                    canViewLogs(status) {
                        return ['queued', 'ringing', 'answered', 'completed', 'failed', 'missed'].includes(status);
                    },

                    fmtDate(iso) {
                        if (!iso) { return '—'; }
                        return new Date(iso).toLocaleString();
                    },

                    // datetime-local yields naive wall-clock (no tz); convert to a UTC ISO instant
                    // so the backend stores the correct moment and it reads back in the same local time.
                    toIsoInstant(local) {
                        if (!local) { return null; }
                        const d = new Date(local);
                        return isNaN(d) ? null : d.toISOString();
                    },

                    // UTC ISO instant -> naive local wall-clock string a datetime-local input accepts.
                    toLocalInput(iso) {
                        if (!iso) { return ''; }
                        const d = new Date(iso);
                        if (isNaN(d)) { return ''; }
                        const pad = (n) => String(n).padStart(2, '0');
                        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                    },

                    async viewLogs(uuid) {
                        this.logsCall = null;
                        this.logsLoading = true;

                        const modal = document.querySelector('#call-logs-modal');
                        if (modal) { Alpine.$data(modal).modalOpen = true; }

                        try {
                            const res = await fetch(`/dashboard/phone-call-agent/outbound/calls/${uuid}/logs`, {
                                method: 'GET',
                                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                toastr.error(data.message || '{{ __('phone-call-agent::phone-call-agent.logs_load_failed') }}');
                                return;
                            }

                            this.logsCall = data.call || null;
                        } catch (e) {
                            toastr.error('{{ __('phone-call-agent::phone-call-agent.logs_load_failed') }}');
                        } finally {
                            this.logsLoading = false;
                        }
                    },

                    // A pending row whose schedule has passed. Display-only: the backend status stays
                    // "pending", so filters and queries are unaffected — but "Scheduled" on an overdue
                    // row is indistinguishable from a healthy future one, which is how a stalled call
                    // goes unnoticed.
                    isWaiting(call) {
                        return call
                            && call.status === 'pending'
                            && call.scheduled_at
                            && new Date(call.scheduled_at) < new Date();
                    },

                    statusLabel(call) {
                        if (this.isWaiting(call)) { return '{{ __('Waiting') }}'; }

                        return {
                            pending: '{{ __('Scheduled') }}',
                            queued: '{{ __('In Progress') }}',
                            answered: '{{ __('In Progress') }}',
                            completed: '{{ __('Completed') }}',
                            failed: '{{ __('Failed') }}',
                            missed: '{{ __('Missed') }}',
                            cancelled: '{{ __('Cancelled') }}',
                            ringing: '{{ __('In Progress') }}',
                        }[call?.status] || call?.status;
                    },

                    statusClass(call) {
                        if (this.isWaiting(call)) { return 'bg-amber-500/15 text-amber-600'; }

                        return {
                            completed: 'bg-green-500/15 text-green-600',
                            answered: 'bg-blue-500/15 text-blue-600',
                            queued: 'bg-yellow-500/15 text-yellow-600',
                            ringing: 'bg-yellow-500/15 text-yellow-600',
                            pending: 'bg-primary/10 text-primary',
                            missed: 'bg-orange-500/15 text-orange-600',
                            failed: 'bg-red-500/15 text-red-600',
                            cancelled: 'bg-heading-foreground/10 text-heading-foreground/60',
                        }[call?.status] || 'bg-heading-foreground/10 text-heading-foreground';
                    },

                    // The banner text for a waiting row: the recorded reason, or the absence of one,
                    // which itself says the dispatcher never reached the row.
                    waitingReason(call) {
                        return call?.status_reason
                            || '{{ __('phone-call-agent::phone-call-agent.outbound_waiting_no_reason') }}';
                    },

                    whenLabel(call) {
                        const iso = call.started_at || call.scheduled_at || call.created_at;
                        if (!iso) { return '—'; }
                        const d = new Date(iso);
                        const prefix = (!call.started_at && call.scheduled_at) ? '{{ __('Scheduled') }}: ' : '';
                        return prefix + d.toLocaleString();
                    },

                    formatDuration(seconds) {
                        const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                        const s = (seconds % 60).toString().padStart(2, '0');
                        return `${m}:${s}`;
                    },

                    async placeCall() {
                        @if ($app_is_demo)
                            toastr.error('{{ __('phone-call-agent::phone-call-agent.demo_mode_disabled') }}');
                            return;
                        @endif

                        const to = (this.callForm.to_number || '').trim();
                        if (!this.selectedAgent) { toastr.warning('{{ __('From Agent') }}'); return; }
                        if (!to) { toastr.warning('{{ __('phone-call-agent::phone-call-agent.outbound_number_required') }}'); return; }
                        if (this.busy) { return; }
                        this.busy = true;

                        try {
                            const res = await fetch('{{ route('dashboard.phone-call-agent.outbound.call') }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    agent_id: this.selectedAgent,
                                    to_number: to,
                                    scheduled_at: this.toIsoInstant(this.callForm.scheduled_at),
                                }),
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                toastr.error(data.message || Object.values(data.errors || {}).flat()[0] || 'Error');
                                return;
                            }

                            toastr.success(data.message);
                            this.callForm = { to_number: '', scheduled_at: '' };
                            this.closeModal('#place-call-modal');
                            this.loadCalls(1);
                        } catch (e) {
                            toastr.error('{{ __('phone-call-agent::phone-call-agent.outbound_provider_failed') }}');
                        } finally {
                            this.busy = false;
                        }
                    },

                    async startCampaign() {
                        @if ($app_is_demo)
                            toastr.error('{{ __('phone-call-agent::phone-call-agent.demo_mode_disabled') }}');
                            return;
                        @endif

                        const numbers = (this.campaignForm.numbers || '')
                            .split(/[\n,]+/)
                            .map(n => n.trim())
                            .filter(Boolean);

                        if (!this.selectedAgent) { toastr.warning('{{ __('From Agent') }}'); return; }
                        if (!this.campaignForm.name.trim()) { toastr.warning('{{ __('Campaign Name') }}'); return; }
                        if (!numbers.length) { toastr.warning('{{ __('phone-call-agent::phone-call-agent.campaign_no_numbers') }}'); return; }
                        if (this.busy) { return; }
                        this.busy = true;

                        try {
                            const res = await fetch('{{ route('dashboard.phone-call-agent.outbound.campaigns.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    agent_id: this.selectedAgent,
                                    name: this.campaignForm.name,
                                    numbers,
                                    scheduled_at: this.toIsoInstant(this.campaignForm.scheduled_at),
                                }),
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                toastr.error(data.message || Object.values(data.errors || {}).flat()[0] || 'Error');
                                return;
                            }

                            toastr.success(data.message);
                            this.campaignForm = { name: '', numbers: '', scheduled_at: '' };
                            this.closeModal('#new-campaign-modal');
                            this.loadCampaigns();
                            this.loadCalls(1);
                        } catch (e) {
                            toastr.error('Error');
                        } finally {
                            this.busy = false;
                        }
                    },

                    async loadCampaigns() {
                        try {
                            const res = await fetch('{{ route('dashboard.phone-call-agent.outbound.campaigns') }}', {
                                method: 'GET',
                                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            });
                            const data = await res.json();
                            this.campaigns = res.ok ? (data.campaigns || []) : [];
                        } catch (e) {
                            this.campaigns = [];
                        }
                    },

                    async cancelCampaign(uuid) {
                        @if ($app_is_demo)
                            toastr.error('{{ __('phone-call-agent::phone-call-agent.demo_mode_disabled') }}');
                            return;
                        @endif

                        if (!confirm('{{ __('Cancel this batch call? Pending calls will not be placed.') }}')) { return; }

                        const res = await fetch(`/dashboard/phone-call-agent/outbound/campaigns/${uuid}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                        });
                        const data = await res.json();

                        if (!res.ok) {
                            toastr.error(data.message || 'Error');
                            return;
                        }

                        toastr.success(data.message);
                        this.loadCampaigns();
                        this.loadCalls(1);
                    },

                    async openCampaignDetails(uuid) {
                        this.detailsCampaign = null;
                        this.detailsLoading = true;

                        const modal = document.querySelector('#campaign-details-modal');
                        if (modal) { Alpine.$data(modal).modalOpen = true; }

                        try {
                            const res = await fetch(`/dashboard/phone-call-agent/outbound/campaigns/${uuid}`, {
                                method: 'GET',
                                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                toastr.error(data.message || 'Error');
                                this.closeModal('#campaign-details-modal');
                                return;
                            }

                            this.detailsCampaign = data.campaign || null;
                        } catch (e) {
                            toastr.error('Error');
                            this.closeModal('#campaign-details-modal');
                        } finally {
                            this.detailsLoading = false;
                        }
                    },

                    async openEditCampaign(uuid) {
                        this.editCampaignForm = { uuid, name: '', numbers: '', scheduled_at: '' };
                        this.editCampaignLoading = true;

                        const modal = document.querySelector('#edit-campaign-modal');
                        if (modal) { Alpine.$data(modal).modalOpen = true; }

                        try {
                            const res = await fetch(`/dashboard/phone-call-agent/outbound/campaigns/${uuid}`, {
                                method: 'GET',
                                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                toastr.error(data.message || 'Error');
                                this.closeModal('#edit-campaign-modal');
                                return;
                            }

                            const c = data.campaign || {};
                            this.editCampaignForm = {
                                uuid,
                                name: c.name || '',
                                numbers: (c.numbers || []).join('\n'),
                                scheduled_at: this.toLocalInput(c.scheduled_at),
                            };
                        } catch (e) {
                            toastr.error('Error');
                            this.closeModal('#edit-campaign-modal');
                        } finally {
                            this.editCampaignLoading = false;
                        }
                    },

                    async submitEditCampaign() {
                        @if ($app_is_demo)
                            toastr.error('{{ __('phone-call-agent::phone-call-agent.demo_mode_disabled') }}');
                            return;
                        @endif

                        const numbers = (this.editCampaignForm.numbers || '')
                            .split(/[\n,]+/)
                            .map(n => n.trim())
                            .filter(Boolean);

                        if (!this.editCampaignForm.name.trim()) { toastr.warning('{{ __('Campaign Name') }}'); return; }
                        if (!numbers.length) { toastr.warning('{{ __('phone-call-agent::phone-call-agent.campaign_no_numbers') }}'); return; }
                        if (this.busy) { return; }
                        this.busy = true;

                        try {
                            const res = await fetch(`/dashboard/phone-call-agent/outbound/campaigns/${this.editCampaignForm.uuid}`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    name: this.editCampaignForm.name,
                                    numbers,
                                    scheduled_at: this.toIsoInstant(this.editCampaignForm.scheduled_at),
                                }),
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                toastr.error(data.message || Object.values(data.errors || {}).flat()[0] || 'Error');
                                return;
                            }

                            toastr.success(data.message);
                            this.closeModal('#edit-campaign-modal');
                            this.loadCampaigns();
                            this.loadCalls(1);
                        } catch (e) {
                            toastr.error('Error');
                        } finally {
                            this.busy = false;
                        }
                    },
                }));
            });
        })();
    </script>
@endpush
