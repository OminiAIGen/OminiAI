<?php

declare(strict_types=1);

namespace App\Domains\Engine\Services;

use App\Domains\Entity\Enums\EntityEnum;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use Generator;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AnthropicService
{
    public ?string $key = null;

    public bool $stream = false;

    public array $messages = [];

    public ?string $system = null;

    public array $tools = [];

    public ?string $model = null;

    public const ENDPOINT = 'https://api.anthropic.com/v1/messages';

    /**
     * Seconds to wait for the connection to open.
     */
    public const CONNECT_TIMEOUT = 10;

    /**
     * Seconds a streamed answer may stay silent before it fails, the same as OpenAI's request_timeout.
     * There is no cap on the whole answer.
     */
    public const READ_TIMEOUT = 120;

    public function stream(): PromiseInterface|Response
    {
        $client = $this->client();

        $system = (bool) $this->system;

        $hasTools = ! empty($this->tools);

        $body = Helper::arrayMerge($system, [
            'model'      => $this->requestModel(),
            'max_tokens' => (int) setting('anthropic_max_output_length', 1024),
            'messages'   => $this->messages,
            'stream'     => $this->isStream(),
        ], [
            'system' => $this->system,
        ]);

        if ($hasTools) {
            $body['tools'] = $this->tools;
        }

        if ($this->isStream()) {
            $client->connectTimeout(self::CONNECT_TIMEOUT)
                ->timeout(self::READ_TIMEOUT)
                ->withOptions(['stream' => true, 'read_timeout' => self::READ_TIMEOUT]);
        }

        return $client->post(self::ENDPOINT, $body);
    }

    /**
     * Sends the streamed request and yields the answer's lines as the provider sends them.
     *
     * @return Generator<int, string>
     *
     * @throws ConnectionException when the provider can't be reached
     * @throws RequestException when the provider answers with an error status
     * @throws RuntimeException when the provider stays silent longer than READ_TIMEOUT
     */
    public function streamLines(): Generator
    {
        $body = $this->setStream(true)->stream()->throw()->toPsrResponse()->getBody();
        $buffer = '';

        try {
            while (! $body->eof()) {
                $chunk = $body->read(8192);

                if ($chunk === '') {
                    if ($body->getMetadata('timed_out')) {
                        throw new RuntimeException('Anthropic stream sent nothing for ' . self::READ_TIMEOUT . ' seconds.');
                    }

                    continue;
                }

                $buffer .= $chunk;

                while (($end = strpos($buffer, "\n")) !== false) {
                    yield substr($buffer, 0, $end);
                    $buffer = substr($buffer, $end + 1);
                }
            }

            if ($buffer !== '') {
                yield $buffer;
            }
        } finally {
            $body->close();
        }
    }

    public function client(): PendingRequest
    {
        return Http::withHeaders([
            'x-api-key'         => $this->getKey(),
            'Accept'            => 'application/json',
            'Content-Type'      => 'application/json',
            'anthropic-version' => '2023-06-01',
        ]);
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key ?? ApiHelper::setAnthropicKey();
    }

    public function isStream(): bool
    {
        return $this->stream;
    }

    public function setStream(bool $stream): self
    {
        $this->stream = $stream;

        return $this;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    public function setSystem(?string $system): self
    {
        $this->system = $system;

        return $this;
    }

    public function setTools(array $tools): self
    {
        $this->tools = $tools;

        return $this;
    }

    /**
     * The model to ask; without one, the admin's default Anthropic model.
     */
    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * The model set with setModel(), unless Anthropic retired it; then the admin's default.
     */
    private function requestModel(): ?string
    {
        if ($this->model && ! EntityEnum::tryFrom($this->model)?->isRetiredByProvider()) {
            return $this->model;
        }

        return setting('anthropic_default_model', EntityEnum::CLAUDE_SONNET_5_5->value);
    }
}
