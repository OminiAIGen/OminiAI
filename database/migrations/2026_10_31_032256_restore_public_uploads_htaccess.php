<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * The public/uploads/.htaccess shipped since V11.3. It stops Apache from running scripts
     * placed under public/uploads. Updates never touch public/uploads, so installs from before
     * V11.3 that were updated in place don't have it.
     */
    private const HTACCESS = <<<'HTACCESS'
Options -ExecCGI

<IfModule mod_mime.c>
    RemoveHandler .php .php3 .php4 .php5 .php7 .php8 .phtml .pht .phar .phps .cgi .shtml .asp .aspx .jsp
    RemoveType .php .php3 .php4 .php5 .php7 .php8 .phtml .pht .phar .phps .cgi .shtml .asp .aspx .jsp
</IfModule>

<FilesMatch "(?i)(^\.|\.(php[0-9]*|phtml|pht|phar|phps|cgi|shtml|asp|aspx|jsp|user\.ini)$)">
    Require all denied
</FilesMatch>
HTACCESS;

    /**
     * Create public/uploads/.htaccess when it is missing. An existing file is never replaced, since
     * it may be customised, and a read-only or permission problem only logs a warning so the
     * update itself still succeeds.
     */
    public function up(): void
    {
        $path = public_path('uploads/.htaccess');

        if (file_exists($path)) {
            return;
        }

        try {
            if (! is_dir(dirname($path)) && ! mkdir(dirname($path), 0755, true)) {
                throw new RuntimeException('Could not create ' . dirname($path));
            }

            // Mode "x" refuses to open a file that already exists, so a file created in the
            // meantime is never overwritten either.
            $handle = fopen($path, 'x');

            if ($handle === false) {
                throw new RuntimeException('Could not create ' . $path);
            }

            $contents = self::HTACCESS . "\n";
            $written = fwrite($handle, $contents);
            fclose($handle);

            if ($written !== strlen($contents)) {
                unlink($path);

                throw new RuntimeException('Could not write ' . $path);
            }
        } catch (Throwable $exception) {
            Log::warning('Could not create public/uploads/.htaccess. Copy it from the MagicAI download to stop scripts running from the uploads folder.', [
                'path'  => $path,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function down(): void {}
};
