<?php

declare(strict_types=1);

namespace App\Extensions\MarketingBot\System\Services\Chat;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Helpers\Classes\ApiHelper;
use App\Models\Usage;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use OpenAI\Contracts\ClientContract;

/**
 * Streams a tool-calling copilot conversation for the Marketing Agent.
 *
 * Mirrors the AIAgent copilot streaming pattern but is self-contained inside the
 * MarketingBot extension so it carries no dependency on the AIAgent extension.
 * Uses OpenAI native function calling; each tool call is executed server-side via
 * the provided executor (the draft campaign is the source of truth) and the result
 * is fed back into the model, producing a Zapier-like activity log.
 */
class MarketingCopilotChatService
{
    protected null|int|User $creditUser = null;

    public function forUser(null|int|User $user): static
    {
        $this->creditUser = $user;

        return $this;
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<int, array<string, mixed>>  $tools
     * @param  callable(string $type, array $payload): void  $onChunk
     * @param  callable(string $name, array $args): array<string, mixed>  $toolExecutor
     */
    public function streamCopilot(
        string $systemPrompt,
        array $messages,
        EntityEnum $model,
        callable $onChunk,
        array $tools,
        callable $toolExecutor,
    ): void {
        if ($this->creditUser !== null && ! Entity::driver($model)->forUser($this->creditUser)->hasCreditBalance()) {
            $onChunk('error', ['message' => __('You have no credits left. Please consider upgrading your plan.')]);
            $onChunk('done', []);

            return;
        }

        $apiMessages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $messages,
        );

        try {
            $accumulated = $this->runAgenticLoop($this->makeOpenAiClient(), $model, $apiMessages, $tools, $onChunk, $toolExecutor);
            $this->deductCredits($accumulated, $model);
        } catch (Exception $e) {
            $onChunk('error', ['message' => $e->getMessage()]);
            $onChunk('done', []);
        }
    }

    /**
     * Build an OpenAI client for streaming. Overridable for testing.
     */
    protected function makeOpenAiClient(): ClientContract
    {
        ApiHelper::setOpenAiKey();

        return app(ClientContract::class);
    }

    /**
     * @param  array<int, array<string, mixed>>  $tools
     * @param  callable(string $type, array $payload): void  $onChunk
     * @param  callable(string $name, array $args): array<string, mixed>  $toolExecutor
     */
    private function runAgenticLoop(
        ClientContract $client,
        EntityEnum $model,
        array $messages,
        array $tools,
        callable $onChunk,
        callable $toolExecutor,
        int $maxIterations = 8,
    ): string {
        $totalText = '';

        for ($i = 0; $i < $maxIterations; $i++) {
            $stream = $client->chat()->createStreamed([
                'model'    => $model->value,
                'messages' => $messages,
                'tools'    => $tools,
            ]);

            $onChunk('thinking_start', []);
            $fullText = '';
            $toolCallAccumulator = [];

            foreach ($stream as $chunk) {
                $delta = $chunk->choices[0]->delta;

                $content = $delta->content ?? null;
                if ($content !== null && $content !== '') {
                    $fullText .= $content;
                }

                foreach ($delta->toolCalls ?? [] as $tcDelta) {
                    $idx = $tcDelta->index;
                    if (! isset($toolCallAccumulator[$idx])) {
                        $toolCallAccumulator[$idx] = ['id' => '', 'name' => '', 'arguments' => ''];
                    }
                    if (! empty($tcDelta->id)) {
                        $toolCallAccumulator[$idx]['id'] = $tcDelta->id;
                    }
                    if (! empty($tcDelta->function->name)) {
                        $toolCallAccumulator[$idx]['name'] = $tcDelta->function->name;
                    }
                    if (! empty($tcDelta->function->arguments)) {
                        $toolCallAccumulator[$idx]['arguments'] .= $tcDelta->function->arguments;
                    }
                }
            }

            $onChunk('thinking_end', []);

            if ($fullText !== '') {
                $totalText .= $fullText;
                $onChunk('text_delta', ['text' => $fullText]);
            }

            if (empty($toolCallAccumulator)) {
                break;
            }

            $assistantToolCalls = [];
            $toolResultMessages = [];

            foreach ($toolCallAccumulator as $tc) {
                $args = json_decode($tc['arguments'], true) ?: [];
                $id = $tc['id'] ?: (string) Str::uuid();

                if ($tc['name'] === 'emit_action_buttons') {
                    $onChunk('action_buttons', ['buttons' => $args['buttons'] ?? []]);
                    $result = ['success' => true];
                } else {
                    $onChunk('tool_call_start', ['name' => $tc['name'], 'id' => $id]);
                    $result = $toolExecutor($tc['name'], $args);
                    $onChunk('tool_call_end', [
                        'name'   => $tc['name'],
                        'id'     => $id,
                        'args'   => array_merge($args, is_array($result) ? $result : []),
                        'status' => 'success',
                    ]);
                }

                $assistantToolCalls[] = [
                    'id'       => $id,
                    'type'     => 'function',
                    'function' => [
                        'name'      => $tc['name'],
                        'arguments' => $tc['arguments'] ?: '{}',
                    ],
                ];

                $toolResultMessages[] = [
                    'role'         => 'tool',
                    'tool_call_id' => $id,
                    'content'      => json_encode($result),
                ];
            }

            $messages[] = [
                'role'       => 'assistant',
                'content'    => $fullText !== '' ? $fullText : null,
                'tool_calls' => $assistantToolCalls,
            ];
            $messages = array_merge($messages, $toolResultMessages);
        }

        $onChunk('done', []);

        return $totalText;
    }

    private function deductCredits(string $responseText, ?EntityEnum $model): void
    {
        if ($this->creditUser === null || $model === null || $responseText === '') {
            return;
        }

        $driver = Entity::driver($model)->forUser($this->creditUser);
        $driver->input($responseText)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());
    }
}
