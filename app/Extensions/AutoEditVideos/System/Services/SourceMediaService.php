<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Services;

use App\Extensions\AutoEditVideos\System\Enums\SourceTypeEnum;
use App\Extensions\AutoEditVideos\System\Models\AutoEditSource;
use App\Models\User;
use getID3;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Turns whatever the user gave us into a row Rendley can actually fetch.
 *
 * Rendley pulls every source over the public internet, so the hard part is not
 * storing the file — it is proving the URL we hand over is reachable from
 * outside this server. A local host fails inside Rendley with
 * MEDIA_MISSING_PERMANENT_URL, which reads as a vendor outage, so we reject it
 * here with a message that names the setting to fix.
 */
class SourceMediaService
{
    /**
     * Persist an uploaded file and probe it.
     *
     * @return AutoEditSource|string the source, or the reason it was refused
     */
    public function storeUpload(User $user, UploadedFile $file): AutoEditSource|string
    {
        $disk = (string) config('auto-edit-videos.storage.disk', 'uploads');

        $directory = sprintf(
            '%s/u-%d/auto-edit',
            trim((string) config('auto-edit-videos.storage.directory', 'media/videos'), '/'),
            $user->id,
        );

        $name = Str::uuid() . '.' . ($file->getClientOriginalExtension() ?: 'mp4');
        $path = $file->storeAs($directory, $name, $disk);

        if (! is_string($path) || $path === '') {
            return __('The file could not be saved. Please try again.');
        }

        try {
            Storage::disk($disk)->setVisibility($path, 'public');
        } catch (Throwable) {
        }

        $publicUrl = $this->publicUrlFor($disk, $path);
        $reachable = $this->assertReachable($publicUrl);

        if ($reachable !== true) {
            Storage::disk($disk)->delete($path);

            return $reachable;
        }

        $probe = $this->probe(Storage::disk($disk)->path($path));

        return AutoEditSource::query()->create([
            'user_id'          => $user->id,
            'type'             => SourceTypeEnum::Upload,
            'original_name'    => $file->getClientOriginalName(),
            'local_path'       => $path,
            'public_url'       => $publicUrl,
            'mime_type'        => $file->getClientMimeType(),
            'size_bytes'       => $file->getSize(),
            'duration_seconds' => $probe['duration'],
            'width'            => $probe['width'],
            'height'           => $probe['height'],
        ]);
    }

    /**
     * Register a URL the user pasted. Nothing is copied locally — Rendley
     * fetches it directly, so all we owe the user is an early, honest check.
     *
     * @return AutoEditSource|string the source, or the reason it was refused
     */
    public function storeUrl(User $user, string $url): AutoEditSource|string
    {
        $url = trim($url);

        $reachable = $this->assertReachable($url);

        if ($reachable !== true) {
            return $reachable;
        }

        $head = $this->head($url);

        return AutoEditSource::query()->create([
            'user_id'       => $user->id,
            'type'          => SourceTypeEnum::Url,
            'original_name' => basename((string) parse_url($url, PHP_URL_PATH)) ?: $url,
            'public_url'    => $url,
            'mime_type'     => $head['mime'],
            'size_bytes'    => $head['size'],
        ]);
    }

    /**
     * The URL Rendley should fetch. An administrator can override the host when
     * the app is served internally but has a public mirror or CDN in front.
     *
     * Both the `uploads` and legacy `public` disks share the same root
     * (`public/uploads`), but only `uploads` builds a servable URL
     * (`APP_URL/uploads/...`). The `public` disk builds `APP_URL/public/...`,
     * which 404s, so legacy paths are repaired here as well.
     */
    public function publicUrlFor(string $disk, string $path): string
    {
        $path = ltrim($path, '/');
        $override = trim((string) setting('auto_edit_videos_public_base_url', ''));

        if ($override !== '') {
            $base = rtrim($override, '/');

            if (! preg_match('#^https?://#i', $base)) {
                $base = 'https://' . $base;
            }

            return $base . '/' . $path;
        }

        $driver = (string) config("filesystems.disks.{$disk}.driver");

        if ($driver === 'local' && in_array($disk, ['uploads', 'public'], true)) {
            return rtrim((string) config('app.url'), '/') . '/uploads/' . $path;
        }

        $url = Storage::disk($disk)->url($path);

        if ($disk === 'public') {
            $url = str_replace('/public/', '/uploads/', $url);
        }

        if (! preg_match('#^https?://#i', $url)) {
            $url = rtrim((string) config('app.url'), '/') . '/' . ltrim($url, '/');
        }

        return $url;
    }

    /**
     * @return true|string true, or why Rendley would never be able to fetch it
     */
    public function assertReachable(string $url): true|string
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if ($host === '' || ! in_array($scheme, ['http', 'https'], true)) {
            return __('That does not look like a valid media URL.');
        }

        if (in_array($host, (array) config('auto-edit-videos.blocked_hosts', []), true)) {
            return $this->unreachableMessage();
        }

        foreach ((array) config('auto-edit-videos.blocked_host_suffixes', []) as $suffix) {
            if (str_ends_with($host, (string) $suffix)) {
                return $this->unreachableMessage();
            }
        }

        if ($this->isPrivateAddress($host)) {
            return $this->unreachableMessage();
        }

        $status = $this->probeUrlStatus($url);

        if ($status === null) {
            return __('Rendley has to download your media over the public internet, but this URL could not be reached. Ask an administrator to set a public media URL in the Auto Edit Videos settings, or paste a public link instead.');
        }

        if ($status < 200 || $status >= 300) {
            return __('Rendley has to download your media over the public internet, but this URL returned a :status code. Ensure the file is publicly accessible without authentication.', [
                'status' => $status,
            ]);
        }

        return true;
    }

    /**
     * Some servers and CDNs answer HEAD with 404/405 while GET works fine, so
     * fall back to a one-byte ranged GET before calling a URL unreachable.
     *
     * @return int|null HTTP status, or null when the host could not be reached
     */
    private function probeUrlStatus(string $url): ?int
    {
        try {
            $response = Http::timeout(10)->head($url);

            if ($response->successful()) {
                return $response->status();
            }

            if (! in_array($response->status(), [404, 405], true)) {
                return $response->status();
            }
        } catch (Throwable) {
        }

        try {
            $response = Http::timeout(10)->withHeaders(['Range' => 'bytes=0-0'])->get($url);

            // 206 (partial) and 200 both prove the file is servable; some
            // servers ignore Range and return the whole file with 200.
            if ($response->successful()) {
                return $response->status();
            }

            // 416 means the file exists but the range was unsatisfiable
            // (e.g. empty file) — still reachable for Rendley.
            if ($response->status() === 416) {
                return 200;
            }

            return $response->status();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Read duration and dimensions from a local file.
     *
     * @return array{duration: int|null, width: int|null, height: int|null}
     */
    public function probe(string $absolutePath): array
    {
        $empty = ['duration' => null, 'width' => null, 'height' => null];

        if (! is_file($absolutePath)) {
            return $empty;
        }

        try {
            $analyzer = new getID3;
            $info = $analyzer->analyze($absolutePath);

            $duration = isset($info['playtime_seconds']) ? (int) ceil((float) $info['playtime_seconds']) : null;

            return [
                'duration' => $duration !== null && $duration > 0 ? $duration : null,
                'width'    => isset($info['video']['resolution_x']) ? (int) $info['video']['resolution_x'] : null,
                'height'   => isset($info['video']['resolution_y']) ? (int) $info['video']['resolution_y'] : null,
            ];
        } catch (Throwable $e) {
            Log::warning('AutoEditVideos: could not probe source file', [
                'path'  => $absolutePath,
                'error' => $e->getMessage(),
            ]);

            return $empty;
        }
    }

    /**
     * @param  iterable<AutoEditSource>  $sources
     *
     * @return array<int, string>
     */
    public function urlsFor(iterable $sources): array
    {
        $urls = [];

        foreach ($sources as $source) {
            if (filled($source->public_url)) {
                $urls[] = (string) $source->public_url;
            }
        }

        return $urls;
    }

    /**
     * Best-effort total runtime of the footage handed in, used only as a
     * pre-flight billing estimate before a real render exists.
     *
     * @param  iterable<AutoEditSource>  $sources
     */
    public function totalSeconds(iterable $sources): int
    {
        $total = 0;

        foreach ($sources as $source) {
            $total += (int) $source->duration_seconds;
        }

        return $total;
    }

    /**
     * @return array{mime: string|null, size: int|null}
     */
    private function head(string $url): array
    {
        try {
            $response = Http::timeout(15)->head($url);

            if ($response->successful()) {
                $size = $response->header('Content-Length');

                return [
                    'mime' => $response->header('Content-Type') ?: null,
                    'size' => is_numeric($size) ? (int) $size : null,
                ];
            }
        } catch (Throwable $e) {
            Log::warning('AutoEditVideos: HEAD request failed for source URL', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $response = Http::timeout(15)->withHeaders(['Range' => 'bytes=0-0'])->get($url);

            if (! $response->successful() && $response->status() !== 416) {
                return ['mime' => null, 'size' => null];
            }

            $size = $response->header('Content-Range');

            if (is_string($size) && preg_match('#/(\d+)$#', $size, $matches)) {
                $size = $matches[1];
            } else {
                $size = $response->header('Content-Length');
            }

            return [
                'mime' => $response->header('Content-Type') ?: null,
                'size' => is_numeric($size) ? (int) $size : null,
            ];
        } catch (Throwable $e) {
            Log::warning('AutoEditVideos: GET request failed for source URL', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);

            return ['mime' => null, 'size' => null];
        }
    }

    private function isPrivateAddress(string $host): bool
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE,
            ) === false;
        }

        $resolved = gethostbyname($host);

        if ($resolved !== $host && filter_var($resolved, FILTER_VALIDATE_IP)) {
            return filter_var(
                $resolved,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE,
            ) === false;
        }

        return false;
    }

    private function unreachableMessage(): string
    {
        return __('Rendley has to download your media over the public internet, and this address is not reachable from outside this server. Ask an administrator to set a public media URL in the Auto Edit Videos settings, or paste a public link instead.');
    }
}
