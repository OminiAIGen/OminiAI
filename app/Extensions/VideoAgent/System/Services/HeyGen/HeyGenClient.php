<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen;

use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasAccount;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasAssets;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasAvatars;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasBatches;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasBrand;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasClipping;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasHyperFrames;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasLipsync;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasProofread;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasTemplates;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasTranslations;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasVideoAgents;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasVideos;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasVoices;
use App\Extensions\VideoAgent\System\Services\HeyGen\Concerns\HasWebhooks;
use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Thin client for the HeyGen v3 API.
 *
 * The API key is the core-owned `settings.heygen_secret_key` column, shared
 * with the AiPersona and VideoDubbing extensions. Every method returns a plain
 * array; failures come back as `['error' => string, 'message' => string]` so
 * callers never have to catch.
 */
class HeyGenClient
{
    use HasAccount;
    use HasAssets;
    use HasAvatars;
    use HasBatches;
    use HasBrand;
    use HasClipping;
    use HasHyperFrames;
    use HasLipsync;
    use HasProofread;
    use HasTemplates;
    use HasTranslations;
    use HasVideoAgents;
    use HasVideos;
    use HasVoices;
    use HasWebhooks;

    protected ?string $secretKey;

    public function __construct(?string $secretKey = null)
    {
        $this->secretKey = $secretKey ?? Setting::getCache()?->heygen_secret_key;
    }

    public function isConfigured(): bool
    {
        return filled($this->secretKey);
    }

    /**
     * @param  array<string, mixed>  $query
     *
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->send('get', $path, ['query' => array_filter($query, static fn ($value) => $value !== null)]);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function post(string $path, array $payload = [], bool $idempotent = true): array
    {
        return $this->send('post', $path, ['json' => $payload], $idempotent);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function patch(string $path, array $payload = []): array
    {
        return $this->send('patch', $path, ['json' => $payload]);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function put(string $path, array $payload = []): array
    {
        return $this->send('put', $path, ['json' => $payload]);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $path): array
    {
        return $this->send('delete', $path, []);
    }

    /**
     * Multipart upload used by `POST /v3/assets`.
     *
     * @return array<string, mixed>
     */
    public function upload(string $path, string $contents, string $filename, string $mimeType): array
    {
        if (! $this->isConfigured()) {
            return $this->notConfigured();
        }

        try {
            $response = $this->request()
                ->withHeaders(['Idempotency-Key' => (string) Str::uuid()])
                ->attach('file', $contents, $filename, ['Content-Type' => $mimeType])
                ->post($this->url($path));

            return $this->decode($response, 'POST', $path);
        } catch (Throwable $e) {
            return $this->fail('POST', $path, $e);
        }
    }

    /**
     * Walk a cursor-paginated collection endpoint and return every item.
     *
     * @param  array<string, mixed>  $query
     *
     * @return array<int, array<string, mixed>>
     */
    public function paginate(string $path, array $query = [], int $maxPages = 20): array
    {
        $items = [];

        $this->paginateEach($path, $query, static function (array $page) use (&$items): void {
            $items = array_merge($items, $page);
        }, $maxPages);

        return $items;
    }

    /**
     * Same cursor walk as `paginate()`, but hands each page to the callback
     * instead of accumulating every item in memory. Catalog endpoints return
     * thousands of rows, so callers should persist page by page.
     *
     * @param  array<string, mixed>  $query
     * @param  callable(array<int, array<string, mixed>>): void  $onPage
     */
    public function paginateEach(string $path, array $query, callable $onPage, int $maxPages = 20): bool
    {
        $token = null;

        for ($page = 0; $page < $maxPages; $page++) {
            $response = $this->get($path, $query + ['token' => $token]);

            if (isset($response['error'])) {
                return false;
            }

            $data = $response['data'] ?? [];

            if (is_array($data) && $data !== []) {
                $onPage(array_values($data));
            }

            $token = $response['next_token'] ?? null;

            if (blank($token) || ($response['has_more'] ?? false) === false) {
                return true;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    protected function send(string $method, string $path, array $options, bool $idempotent = false): array
    {
        if (! $this->isConfigured()) {
            return $this->notConfigured();
        }

        try {
            $request = $this->request();

            if ($idempotent) {
                $request = $request->withHeaders(['Idempotency-Key' => (string) Str::uuid()]);
            }

            /** @var Response $response */
            $response = $request->send(strtoupper($method), $this->url($path), $options);

            return $this->decode($response, strtoupper($method), $path);
        } catch (Throwable $e) {
            return $this->fail(strtoupper($method), $path, $e);
        }
    }

    protected function request(): PendingRequest
    {
        return Http::withHeaders([
            'x-api-key' => $this->secretKey,
            'accept'    => 'application/json',
        ])
            ->timeout((int) config('video-agent.api.timeout', 120))
            ->connectTimeout(20)
            ->retry(
                (int) config('video-agent.api.retries', 3),
                fn (int $attempt, Throwable $exception): int             => 500 * $attempt,
                fn (Throwable $exception, PendingRequest $request): bool => $exception instanceof ConnectionException,
                throw: false,
            );
    }

    protected function url(string $path): string
    {
        return rtrim((string) config('video-agent.api.base_url'), '/') . '/' . ltrim($path, '/');
    }

    /**
     * @return array<string, mixed>
     */
    protected function decode(Response $response, string $method, string $path): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $body = $response->json() ?? [];

        Log::error('HeyGen v3 request failed', [
            'method'   => $method,
            'path'     => $path,
            'status'   => $response->status(),
            'response' => $response->body(),
        ]);

        return [
            'error'   => $body['error']['code'] ?? $body['error'] ?? 'request_failed',
            'message' => $this->messageForStatus($response, $body),
            'status'  => $response->status(),
        ];
    }

    /**
     * @param  array<string, mixed>  $body
     */
    protected function messageForStatus(Response $response, array $body): string
    {
        $remote = $body['error']['message'] ?? $body['message'] ?? null;

        return match ($response->status()) {
            401     => __('The HeyGen API key is invalid. Please check the Video Agent settings.'),
            402     => __('The video service is out of credits. Please contact the administrator.'),
            409     => __('This request is already being processed. Please wait a moment and try again.'),
            429     => __('HeyGen rate limit reached. Please try again in a few seconds.'),
            default => is_string($remote) && filled($remote)
                ? $remote
                : __('Something went wrong while talking to HeyGen. Please try again later.'),
        };
    }

    /**
     * @return array<string, mixed>
     */
    protected function fail(string $method, string $path, Throwable $e): array
    {
        Log::error('HeyGen v3 request exception', [
            'method'  => $method,
            'path'    => $path,
            'class'   => $e::class,
            'message' => $e->getMessage(),
        ]);

        return [
            'error'   => 'exception',
            'message' => __('Could not reach HeyGen. Please try again later.'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function notConfigured(): array
    {
        return [
            'error'   => 'not_configured',
            'message' => __('The Video Agent service is not configured yet. Please contact the administrator.'),
        ];
    }
}
