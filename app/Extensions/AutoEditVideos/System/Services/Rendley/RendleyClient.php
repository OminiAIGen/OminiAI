<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Services\Rendley;

use App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns\HasAccount;
use App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns\HasAgent;
use App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns\HasExports;
use App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns\HasJobs;
use App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns\HasProjects;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Thin client for the Rendley REST API.
 *
 * The API key lives in the key/value settings store under
 * `auto_edit_videos_api_key`. Every method returns a plain array; failures come
 * back as `['error' => string, 'message' => string]` so callers never have to
 * catch, matching the house idiom used by the other vendor-API extensions.
 *
 * Rendley wraps every response in `{data, error, meta}`. `decode()` unwraps
 * `data` so callers work with the payload directly.
 *
 * A job payload carries its own `error` field describing why that render
 * failed, which is a completely different thing from this client failing to
 * reach Rendley. Callers must therefore ask `failed()` rather than testing for
 * an `error` key, or a failed render reads as an outage.
 */
class RendleyClient
{
    use HasAccount;
    use HasAgent;
    use HasExports;
    use HasJobs;
    use HasProjects;

    /**
     * Marks a response this client could not complete, as opposed to a payload
     * that merely describes a failure on Rendley's side.
     */
    public const FAILED_KEY = '__rendley_request_failed';

    protected ?string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? (string) setting('auto_edit_videos_api_key', '');
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }

    /**
     * Whether this client could not complete the request. Never test a response
     * for an `error` key directly — a job payload owns that field to describe a
     * render that failed, which is not the same as Rendley being unreachable.
     *
     * @param  array<string, mixed>  $response
     */
    public function failed(array $response): bool
    {
        return ($response[self::FAILED_KEY] ?? false) === true;
    }

    /**
     * @param  array<string, mixed>  $response
     */
    public function messageFrom(array $response, ?string $fallback = null): string
    {
        $message = $response['message'] ?? null;

        return is_string($message) && filled($message)
            ? $message
            : ($fallback ?? __('Something went wrong while talking to Rendley. Please try again later.'));
    }

    /**
     * @param  array<string, mixed>  $query
     *
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->send('get', $this->url($path), [
            'query' => array_filter($query, static fn ($value) => $value !== null),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function post(string $path, array $payload = []): array
    {
        return $this->send('post', $this->url($path), ['json' => $payload]);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function patch(string $path, array $payload = []): array
    {
        return $this->send('patch', $this->url($path), ['json' => $payload]);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $path): array
    {
        return $this->send('delete', $this->url($path), []);
    }

    /**
     * Read from a fully-qualified URL rather than a path under `base_url`.
     *
     * @param  array<string, mixed>  $query
     *
     * @return array<string, mixed>
     */
    public function getAbsolute(string $url, array $query = []): array
    {
        return $this->send('get', $url, [
            'query' => array_filter($query, static fn ($value) => $value !== null),
        ]);
    }

    /**
     * Rendley documents the auto-edit endpoint on a different host from the
     * rest of the API, so it is posted to an absolute configured URL rather
     * than a path appended to `base_url`.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function postAbsolute(string $url, array $payload = []): array
    {
        return $this->send('post', $url, ['json' => $payload]);
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    protected function send(string $method, string $url, array $options): array
    {
        if (! $this->isConfigured()) {
            return $this->notConfigured();
        }

        try {
            /** @var Response $response */
            $response = $this->request()->send(strtoupper($method), $url, $options);

            return $this->decode($response, strtoupper($method), $url);
        } catch (Throwable $e) {
            return $this->fail(strtoupper($method), $url, $e);
        }
    }

    protected function request(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ])
            ->timeout((int) config('auto-edit-videos.api.timeout', 120))
            ->connectTimeout(20)
            ->retry(
                (int) config('auto-edit-videos.api.retries', 3),
                fn (int $attempt): int                                   => 500 * $attempt,
                fn (Throwable $exception, PendingRequest $request): bool => $exception instanceof ConnectionException,
                throw: false,
            );
    }

    protected function url(string $path): string
    {
        return rtrim((string) config('auto-edit-videos.api.base_url'), '/') . '/' . ltrim($path, '/');
    }

    /**
     * @return array<string, mixed>
     */
    protected function decode(Response $response, string $method, string $url): array
    {
        $body = $response->json() ?? [];

        if ($response->successful()) {
            $data = $body['data'] ?? $body;

            // A collection endpoint returns a list, which callers still need
            // to receive as an array they can iterate.
            return is_array($data) ? $data : ['value' => $data];
        }

        Log::error('Rendley request failed', [
            'method'   => $method,
            'url'      => $url,
            'status'   => $response->status(),
            'response' => $response->body(),
        ]);

        return [
            self::FAILED_KEY => true,
            'error'          => $body['error']['code'] ?? $body['error'] ?? 'request_failed',
            'message'        => $this->messageForStatus($response, $body),
            'status'         => $response->status(),
        ];
    }

    /**
     * @param  array<string, mixed>  $body
     */
    protected function messageForStatus(Response $response, array $body): string
    {
        $remote = $body['error']['message'] ?? $body['message'] ?? null;

        return match ($response->status()) {
            401, 403 => __('The Rendley API key is invalid. Please check the Auto Edit Videos settings.'),
            402      => __('The video service is out of credits. Please contact the administrator.'),
            404      => $this->notFoundMessage($body),
            429      => __('Rendley rate limit reached. Please try again in a few seconds.'),
            default  => is_string($remote) && filled($remote)
                ? $remote
                : __('Something went wrong while talking to Rendley. Please try again later.'),
        };
    }

    /**
     * Rendley answers a real missing resource with its JSON envelope, but a
     * request to a path it does not route with a plain-text 404. Saying "could
     * not find that project" for the second one points whoever reads it at the
     * project ID instead of at the endpoint URL that is actually wrong.
     *
     * @param  array<string, mixed>  $body
     */
    protected function notFoundMessage(array $body): string
    {
        return filled($body['error'] ?? null)
            ? __('Rendley could not find that project or job.')
            : __('The Rendley endpoint was not found. Check the agent endpoint URL in the Auto Edit Videos settings.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function fail(string $method, string $url, Throwable $e): array
    {
        Log::error('Rendley request exception', [
            'method'  => $method,
            'url'     => $url,
            'class'   => $e::class,
            'message' => $e->getMessage(),
        ]);

        return [
            self::FAILED_KEY => true,
            'error'          => 'exception',
            'message'        => __('Could not reach Rendley. Please try again later.'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function notConfigured(): array
    {
        return [
            self::FAILED_KEY => true,
            'error'          => 'not_configured',
            'message'        => __('Auto Edit Videos is not configured yet. Please contact the administrator.'),
        ];
    }
}
