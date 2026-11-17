<?php

namespace App\Console\Commands;

use App\Domains\Engine\Services\FalAIService;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FluxProQueueCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:flux-pro-queue-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        self::updateFluxProImages();
    }

    public static function updateFluxProImage(?string $request_id = null): void
    {
        if (is_null($request_id)) {
            return;
        }

        $item = UserOpenai::query()
            ->where('response', 'FL')
            ->where('status', 'IN_QUEUE')
            ->where('request_id', $request_id)
            ->first();

        $output = FalAIService::check($item?->request_id);

        if ($output) {
            $payload = data_get($item, 'payload');

            if ($payload && is_array($payload)) {
                $payload['size'] = data_get($output, 'size');
            }

            $image = data_get($output, 'image.url');

            $image = static::downloadImageToStorage($image, null, (int) ($item?->user_id ?? 0));

            $item?->update([
                'output'  => $image ?: $item?->output,
                'payload' => $payload,
                'status'  => 'COMPLETED',
            ]);
        }
    }

    public static function updateFluxProImages(): void
    {
        UserOpenai::query()
            ->where('response', 'FL')
            ->where('status', 'IN_QUEUE')
            ->whereNotNull('request_id')
            ->get()
            ->each(function ($item) {
                $output = FalAIService::check($item->request_id);

                if ($output) {
                    $payload = data_get($item, 'payload');

                    if ($payload && is_array($payload)) {
                        $payload['size'] = data_get($output, 'size');
                    }

                    $image = data_get($output, 'image.url');

                    $image = static::downloadImageToStorage($image, null, (int) $item->user_id);

                    $item->update([
                        'output'  => $image ?: $item->output,
                        'payload' => $payload,
                        'status'  => 'COMPLETED',
                    ]);
                }
            });
    }

    public static function downloadImageToStorage($url = null, $filename = null, int $userId = 0)
    {
        if (! $url) {
            return null;
        }

        $fileContent = null;

        $isDataUri = str_starts_with((string) $url, 'data:image');

        if ($isDataUri) {
            if (preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.+)$/', $url, $matches)) {
                $fileContent = base64_decode(str_replace(' ', '+', $matches[2]), true) ?: null;
            }
        } else {
            $response = Http::get($url);
            if ($response->successful()) {
                $fileContent = $response->body();
            }
        }

        if ($fileContent !== null) {
            $extension = static::imageExtensionFromContent($fileContent);
            $directory = $userId > 0 ? "media/images/u-{$userId}" : 'media/images/guest';
            $baseName = $filename ?: uniqid('image_', true);
            $finalName = str_ends_with($baseName, ".{$extension}") ? $baseName : "{$baseName}.{$extension}";
            $relativePath = "{$directory}/{$finalName}";

            $image_storage = SettingTwo::getCache()?->ai_image_storage;

            if ($image_storage === 'r2') {
                Storage::disk('r2')->put($relativePath, $fileContent);

                return Storage::disk('r2')->url($relativePath);
            }

            if ($image_storage === 's3') {
                Storage::disk('s3')->put($relativePath, $fileContent);

                return Storage::disk('s3')->url($relativePath);
            }

            Storage::disk('thumbs')->put($finalName, $fileContent);
            $saved = Storage::disk('public')->put($relativePath, $fileContent);

            if ($saved) {
                return '/uploads/' . $relativePath;
            }

            return 'error';
        }

        return null;
    }

    /**
     * Name a downloaded image after what its bytes are, never after the URL, the data URI or
     * the Content-Type: those come from the remote side, and a ".php", ".html" or ".svg" name
     * under public/uploads can run or serve script. Anything but a JPEG, GIF or WebP is stored
     * as .png.
     */
    public static function imageExtensionFromContent(string $content): string
    {
        $size = $content === '' ? false : @getimagesizefromstring($content);

        return match ($size[2] ?? null) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_GIF  => 'gif',
            IMAGETYPE_WEBP => 'webp',
            default        => 'png',
        };
    }
}
