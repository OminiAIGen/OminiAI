<?php

declare(strict_types=1);

namespace App\Extensions\MarketingBot\System\Services\Chat;

use App\Domains\Entity\Enums\EntityEnum;
use App\Extensions\MarketingBot\System\Enums\CampaignType;
use App\Extensions\MarketingBot\System\Models\MarketingCampaign;
use App\Extensions\MarketingBot\System\Models\MarketingCampaignCopilotMessage;
use App\Extensions\MarketingBot\System\Models\Telegram\TelegramContact;
use App\Extensions\MarketingBot\System\Models\Whatsapp\Contact;
use App\Extensions\MarketingBot\System\Models\Whatsapp\ContactList;
use App\Extensions\MarketingBot\System\Models\Whatsapp\Segment;
use App\Helpers\Classes\Helper;
use Illuminate\Support\Carbon;

class MarketingCopilotService
{
    public function __construct(
        private readonly MarketingCopilotChatService $chatService,
    ) {}

    /**
     * Stream a copilot turn for a campaign draft, emitting SSE-shaped events.
     *
     * @param  array<string, mixed>  $state  live builder state posted from the browser
     * @param  array<int, array{role: string, content: string}>  $history
     * @param  callable(array<string, mixed>): void  $emit
     */
    public function streamChat(
        MarketingCampaign $campaign,
        array $state,
        array $history,
        string $userMessage,
        callable $emit,
    ): void {
        $systemPrompt = $this->buildSystemPrompt($campaign, $state);
        $messages = array_merge($history, [['role' => 'user', 'content' => $userMessage]]);

        $modelSlug = (string) setting('marketing-agent-copilot-model', '');
        $model = ($modelSlug !== '' ? EntityEnum::tryFrom($modelSlug) : null) ?? Helper::defaultWordModel();

        $this->chatService
            ->forUser($campaign->user)
            ->streamCopilot(
                systemPrompt: $systemPrompt,
                messages: $messages,
                model: $model,
                onChunk: fn (string $type, array $payload) => $emit(['type' => $type, 'payload' => $payload]),
                tools: $this->buildTools($campaign),
                toolExecutor: fn (string $name, array $args): array => $this->applyToolCall($campaign, $name, $args),
            );
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function buildSystemPrompt(MarketingCampaign $campaign, array $state): string
    {
        $channel = $campaign->type instanceof CampaignType ? $campaign->type->value : (string) $campaign->type;
        $audiences = $this->availableAudiences($campaign);

        $contactLines = empty($audiences['contacts'])
            ? '  (none — the user has not created any contact groups yet)'
            : implode("\n", array_map(fn (array $c) => "  - id={$c['id']}, name=\"{$c['name']}\"", $audiences['contacts']));

        $segmentLines = empty($audiences['segments'])
            ? '  (none)'
            : implode("\n", array_map(fn (array $c) => "  - id={$c['id']}, name=\"{$c['name']}\"", $audiences['segments']));

        $stateJson = json_encode([
            'name'         => $state['name'] ?? $campaign->name,
            'content'      => $state['content'] ?? $campaign->content,
            'contacts'     => $state['contacts'] ?? $campaign->contacts,
            'segments'     => $state['segments'] ?? $campaign->segments,
            'scheduled_at' => $state['scheduled_at'] ?? optional($campaign->scheduled_at)->toDateTimeString(),
            'ai_reply'     => $state['ai_reply'] ?? $campaign->ai_reply,
            'instruction'  => $state['instruction'] ?? $campaign->instruction,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        $segmentsBlock = $channel === CampaignType::whatsapp->value
            ? "Available segments (use set_audience.segments with these ids):\n{$segmentLines}"
            : '';

        $now = Carbon::now()->toDateTimeString();

        return <<<PROMPT
You are the Marketing Agent — an AI assistant that builds a {$channel} broadcast campaign by calling tools to fill the campaign builder. You control the live form the user is looking at.

**Your job**
- Understand the user's marketing intent and populate the campaign fields via tool calls.
- Write compelling, on-brand campaign copy and a short, clear campaign title.
- Recommend audiences, CTAs, and a schedule; optimize wording when asked.
- NEVER invent or assume required values. If something needed is missing (audience, offer details, tone, timing), ask ONE focused follow-up question instead of guessing.
- After changing the builder, briefly confirm in plain language what you set.
- Keep {$channel} best practices in mind (concise, personal; you may use placeholders {first_name}, {last_name}, {phone} inside the content).

**Output format**
- Your chat replies to the user may use normal markdown; it is rendered for them.
- Campaign copy passed to set_campaign_field("content", ...) is delivered verbatim to phones, so it must use {$channel}-native markup ONLY: *bold*, _italic_, ~strike~, ```mono```.
- In campaign copy NEVER use **double asterisks**, # headings, [label](url) markdown links (write the bare URL), or markdown tables.
- Leave the placeholders {first_name}, {last_name}, {phone} exactly as written — do not format or replace them.

**Tools**
- set_campaign_field(field, value): set "name" (campaign title), "content" (message body), or "instruction" (AI auto-reply guidance).
- set_audience(contacts[], segments[]): choose recipient groups by their numeric ids from the lists below.
- set_schedule(mode, scheduled_at): mode "now" to send immediately, or "scheduled" with an ISO-8601 datetime (server timezone, current time is {$now}).
- set_ai_reply(enabled, instruction): toggle automatic AI replies to recipients (WhatsApp).
- emit_action_buttons(buttons[]): render clickable cards (e.g. label "Send now" / "Schedule") when you want the user to confirm a final action. Use action values: send_now, schedule, open_audience.

**Available contacts** (use set_audience.contacts with these ids):
{$contactLines}
{$segmentsBlock}

**Current campaign state**
{$stateJson}
PROMPT;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildTools(MarketingCampaign $campaign): array
    {
        $isWhatsapp = ($campaign->type instanceof CampaignType ? $campaign->type->value : (string) $campaign->type) === CampaignType::whatsapp->value;

        $fields = $isWhatsapp ? ['name', 'content', 'instruction'] : ['name', 'content'];

        $audienceProps = [
            'contacts' => ['type' => 'array', 'items' => ['type' => 'integer'], 'description' => 'Contact group ids to target.'],
        ];

        if ($isWhatsapp) {
            $audienceProps['segments'] = ['type' => 'array', 'items' => ['type' => 'integer'], 'description' => 'Segment ids to further filter the audience.'];
        }

        $tools = [
            [
                'type'     => 'function',
                'function' => [
                    'name'        => 'set_campaign_field',
                    'description' => 'Set a single campaign field.',
                    'parameters'  => [
                        'type'       => 'object',
                        'properties' => [
                            'field' => ['type' => 'string', 'enum' => $fields],
                            'value' => ['type' => 'string'],
                        ],
                        'required' => ['field', 'value'],
                    ],
                ],
            ],
            [
                'type'     => 'function',
                'function' => [
                    'name'        => 'set_audience',
                    'description' => 'Select the recipient groups for the campaign.',
                    'parameters'  => [
                        'type'       => 'object',
                        'properties' => $audienceProps,
                        'required'   => ['contacts'],
                    ],
                ],
            ],
            [
                'type'     => 'function',
                'function' => [
                    'name'        => 'set_schedule',
                    'description' => 'Set whether the campaign sends now or at a scheduled time.',
                    'parameters'  => [
                        'type'       => 'object',
                        'properties' => [
                            'mode'         => ['type' => 'string', 'enum' => ['now', 'scheduled']],
                            'scheduled_at' => ['type' => 'string', 'description' => 'ISO-8601 datetime, required when mode is "scheduled".'],
                        ],
                        'required' => ['mode'],
                    ],
                ],
            ],
            [
                'type'     => 'function',
                'function' => [
                    'name'        => 'emit_action_buttons',
                    'description' => 'Render clickable action cards in the chat for the user to confirm a final action.',
                    'parameters'  => [
                        'type'       => 'object',
                        'properties' => [
                            'buttons' => [
                                'type'  => 'array',
                                'items' => [
                                    'type'       => 'object',
                                    'properties' => [
                                        'label'       => ['type' => 'string'],
                                        'description' => ['type' => 'string'],
                                        'action'      => ['type' => 'string', 'enum' => ['send_now', 'schedule', 'open_audience']],
                                    ],
                                    'required' => ['label', 'action'],
                                ],
                            ],
                        ],
                        'required' => ['buttons'],
                    ],
                ],
            ],
        ];

        if ($isWhatsapp) {
            $tools[] = [
                'type'     => 'function',
                'function' => [
                    'name'        => 'set_ai_reply',
                    'description' => 'Enable or disable automatic AI replies to recipients, with optional instructions.',
                    'parameters'  => [
                        'type'       => 'object',
                        'properties' => [
                            'enabled'     => ['type' => 'boolean'],
                            'instruction' => ['type' => 'string'],
                        ],
                        'required' => ['enabled'],
                    ],
                ],
            ];
        }

        return $tools;
    }

    /**
     * Apply a tool call to the draft campaign (server is the source of truth).
     *
     * @param  array<string, mixed>  $args
     *
     * @return array<string, mixed>
     */
    public function applyToolCall(MarketingCampaign $campaign, string $name, array $args): array
    {
        switch ($name) {
            case 'set_campaign_field':
                $field = $args['field'] ?? null;
                if (in_array($field, ['name', 'content', 'instruction'], true)) {
                    $value = (string) ($args['value'] ?? '');

                    $campaign->{$field} = $field === 'content'
                        ? $this->normalizeChannelMarkup($value)
                        : $value;
                    $campaign->save();

                    return ['success' => true, $field => $campaign->{$field}];
                }

                return ['success' => false, 'error' => 'unknown field'];

            case 'set_audience':
                $contactIds = $this->resolveIds($args['contacts'] ?? [], $this->availableAudiences($campaign)['contacts']);
                $segmentIds = $this->resolveIds($args['segments'] ?? [], $this->availableAudiences($campaign)['segments']);

                $campaign->contacts = $contactIds;
                $campaign->segments = $segmentIds;
                $campaign->save();

                return [
                    'success'    => true,
                    'contacts'   => $contactIds,
                    'segments'   => $segmentIds,
                    'recipients' => $this->resolveRecipients($campaign),
                ];

            case 'set_schedule':
                $mode = $args['mode'] ?? 'now';
                if ($mode === 'scheduled' && ! empty($args['scheduled_at'])) {
                    $campaign->scheduled_at = Carbon::parse((string) $args['scheduled_at']);
                } else {
                    $campaign->scheduled_at = null;
                }
                $campaign->save();

                return [
                    'success'      => true,
                    'mode'         => $mode,
                    'scheduled_at' => optional($campaign->scheduled_at)->toDateTimeString(),
                ];

            case 'set_ai_reply':
                $campaign->ai_reply = (bool) ($args['enabled'] ?? false);
                if (array_key_exists('instruction', $args)) {
                    $campaign->instruction = (string) $args['instruction'];
                }
                $campaign->save();

                return [
                    'success'     => true,
                    'ai_reply'    => $campaign->ai_reply,
                    'instruction' => $campaign->instruction,
                ];

            default:
                return ['success' => false, 'error' => 'unknown tool'];
        }
    }

    /**
     * Rewrite generic markdown into the markup WhatsApp/Telegram actually understand.
     *
     * Campaign copy is delivered to phones verbatim, so `**bold**` would reach recipients
     * as literal asterisks. The system prompt asks for channel-native markup; this is the
     * enforcement, since model output drifts.
     */
    private function normalizeChannelMarkup(string $value): string
    {
        $replacements = [
            '/\*\*(.+?)\*\*/u'           => '*$1*',
            '/__(.+?)__/u'               => '_$1_',
            '/^\h{0,3}#{1,6}\h+/m'       => '',
            '/\[([^]]+)]\(([^)\s]+)\)/u' => '$1 $2',
            '/^\h*\*\h+/m'               => '• ',
        ];

        return (string) preg_replace(array_keys($replacements), array_values($replacements), $value);
    }

    /**
     * @param  array<int, int|string>  $requested
     * @param  array<int, array{id: int, name: string}>  $available
     *
     * @return array<int, int>
     */
    private function resolveIds(array $requested, array $available): array
    {
        $byId = [];
        $byName = [];
        foreach ($available as $a) {
            $byId[(int) $a['id']] = (int) $a['id'];
            $byName[mb_strtolower(trim((string) $a['name']))] = (int) $a['id'];
        }

        $resolved = [];
        foreach ($requested as $value) {
            if (is_numeric($value) && isset($byId[(int) $value])) {
                $resolved[] = $byId[(int) $value];
            } elseif (is_string($value) && isset($byName[mb_strtolower(trim($value))])) {
                $resolved[] = $byName[mb_strtolower(trim($value))];
            }
        }

        return array_values(array_unique($resolved));
    }

    /**
     * Count the real recipients resolved from the campaign's selected groups.
     */
    public function resolveRecipients(MarketingCampaign $campaign): int
    {
        $contacts = (array) $campaign->contacts;
        $segments = (array) $campaign->segments;

        if (($campaign->type instanceof CampaignType ? $campaign->type->value : (string) $campaign->type) === CampaignType::telegram->value) {
            if (empty($contacts)) {
                return 0;
            }

            return (int) TelegramContact::query()
                ->where('user_id', $campaign->user_id)
                ->whereIn('id', $contacts)
                ->count();
        }

        if (empty($contacts)) {
            return 0;
        }

        return (int) ContactList::query()
            ->where('user_id', $campaign->user_id)
            ->whereHas('contacts', fn ($query) => $query->whereIn('contact_id', $contacts))
            ->when($segments, fn ($query) => $query->whereHas('segments', fn ($q) => $q->whereIn('segment_id', $segments)))
            ->count();
    }

    /**
     * @return array{contacts: array<int, array{id: int, name: string}>, segments: array<int, array{id: int, name: string}>}
     */
    private function availableAudiences(MarketingCampaign $campaign): array
    {
        if (($campaign->type instanceof CampaignType ? $campaign->type->value : (string) $campaign->type) === CampaignType::telegram->value) {
            $contacts = TelegramContact::query()
                ->where('user_id', $campaign->user_id)
                ->get(['id', 'name'])
                ->map(fn (TelegramContact $c) => ['id' => (int) $c->id, 'name' => (string) $c->name])
                ->all();

            return ['contacts' => $contacts, 'segments' => []];
        }

        $contacts = Contact::query()
            ->where('user_id', $campaign->user_id)
            ->where('status', 1)
            ->get(['id', 'name'])
            ->map(fn (Contact $c) => ['id' => (int) $c->id, 'name' => (string) $c->name])
            ->all();

        $segments = Segment::query()
            ->where('user_id', $campaign->user_id)
            ->get(['id', 'name'])
            ->map(fn (Segment $s) => ['id' => (int) $s->id, 'name' => (string) $s->name])
            ->all();

        return ['contacts' => $contacts, 'segments' => $segments];
    }

    /**
     * @return array<int, array{role: string, content: string}>
     */
    public function loadHistory(MarketingCampaign $campaign, int $turns = 20): array
    {
        return MarketingCampaignCopilotMessage::query()
            ->where('campaign_id', $campaign->id)
            ->latest()
            ->limit($turns)
            ->get()
            ->reverse()
            ->map(fn (MarketingCampaignCopilotMessage $m) => [
                'role'    => $m->role,
                'content' => $m->content ?? '',
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $toolCalls
     * @param  array<string, mixed>|null  $metadata
     */
    public function saveMessage(MarketingCampaign $campaign, string $role, string $content, ?array $toolCalls = null, ?array $metadata = null): MarketingCampaignCopilotMessage
    {
        return MarketingCampaignCopilotMessage::query()->create([
            'campaign_id' => $campaign->id,
            'role'        => $role,
            'content'     => $content,
            'tool_calls'  => $toolCalls,
            'metadata'    => $metadata,
        ]);
    }
}
