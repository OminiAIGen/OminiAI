<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System;

use App\Domains\Marketplace\Contracts\ExtensionRegisterKeyProviderInterface;
use App\Domains\Marketplace\Contracts\UninstallExtensionServiceProviderInterface;
use App\Extensions\VideoAgent\System\Console\Commands\VideoAgentPollCommand;
use App\Extensions\VideoAgent\System\Http\Controllers\AssetController;
use App\Extensions\VideoAgent\System\Http\Controllers\AvatarController;
use App\Extensions\VideoAgent\System\Http\Controllers\BatchController;
use App\Extensions\VideoAgent\System\Http\Controllers\BrandController;
use App\Extensions\VideoAgent\System\Http\Controllers\HyperFramesController;
use App\Extensions\VideoAgent\System\Http\Controllers\ProofreadController;
use App\Extensions\VideoAgent\System\Http\Controllers\ScriptWriterController;
use App\Extensions\VideoAgent\System\Http\Controllers\SessionController;
use App\Extensions\VideoAgent\System\Http\Controllers\SpeechController;
use App\Extensions\VideoAgent\System\Http\Controllers\StyleController;
use App\Extensions\VideoAgent\System\Http\Controllers\TemplateController;
use App\Extensions\VideoAgent\System\Http\Controllers\ToolsController;
use App\Extensions\VideoAgent\System\Http\Controllers\VideoAgentController;
use App\Extensions\VideoAgent\System\Http\Controllers\VideoAgentSettingController;
use App\Extensions\VideoAgent\System\Http\Controllers\VideoAgentWebhookController;
use App\Extensions\VideoAgent\System\Http\Controllers\VideoController;
use App\Extensions\VideoAgent\System\Http\Controllers\VoiceController;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use App\Http\Middleware\CheckTemplateTypeAndPlan;
use App\Http\Middleware\EnsureVideoAgentIsNotDemo;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class VideoAgentServiceProvider extends ServiceProvider implements ExtensionRegisterKeyProviderInterface, UninstallExtensionServiceProviderInterface
{
    public function register(): void
    {
        $this->registerConfig();

        $this->app->bind(HeyGenClient::class, static fn (): HeyGenClient => new HeyGenClient);
    }

    public function boot(Kernel $kernel): void
    {
        $this->registerTranslations()
            ->registerViews()
            ->registerRoutes()
            ->registerMigrations()
            ->registerCommands()
            ->publishAssets();
    }

    public function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/video-agent.php', 'video-agent');

        return $this;
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'video-agent');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'video-agent');

        return $this;
    }

    public function registerMigrations(): static
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        return $this;
    }

    public function registerCommands(): static
    {
        if (! $this->app->runningInConsole()) {
            return $this;
        }

        $this->commands([VideoAgentPollCommand::class]);

        $this->app->booted(function (): void {
            $this->app->make(Schedule::class)
                ->command('video-agent:poll')
                ->everyMinute()
                ->withoutOverlapping();
        });

        return $this;
    }

    public function publishAssets(): static
    {
        $this->publishes([
            __DIR__ . '/../resources/assets/images' => public_path('vendor/video-agent/images'),
        ], 'extension');

        return $this;
    }

    private function registerRoutes(): static
    {
        $this->registerWebhookRoute()
            ->registerUserRoutes()
            ->registerAdminRoutes();

        return $this;
    }

    /**
     * HeyGen posts here with an HMAC signature instead of a session cookie, so
     * the route is stateless and CSRF-exempt (see VerifyCsrfToken::$except).
     */
    private function registerWebhookRoute(): static
    {
        $this->router()
            ->middleware('api')
            ->post('video-agent/webhook/heygen', VideoAgentWebhookController::class)
            ->withoutMiddleware(['web', 'auth'])
            ->name('video-agent.webhook');

        return $this;
    }

    private function registerUserRoutes(): static
    {
        $this->router()
            ->group([
                'middleware' => ['web', 'auth'],
            ], function (Router $router): void {
                $router->prefix('dashboard/user/video-agent')
                    ->name('dashboard.user.video-agent.')
                    ->middleware([CheckTemplateTypeAndPlan::class, EnsureVideoAgentIsNotDemo::class])
                    ->group(function (Router $router): void {
                        $router->get('/', [VideoAgentController::class, 'index'])->name('index');
                        $router->post('/', [VideoAgentController::class, 'store'])->name('store');
                        $router->get('library', [VideoAgentController::class, 'library'])->name('library');
                        $router->get('check', [VideoAgentController::class, 'check'])->name('check');
                        $router->delete('videos/{video}', [VideoAgentController::class, 'destroy'])->name('videos.destroy');
                        $router->post('videos/bulk-delete', [VideoAgentController::class, 'bulkDestroy'])->name('videos.bulk-delete');

                        $router->get('create', [VideoController::class, 'create'])->name('create');
                        $router->post('create', [VideoController::class, 'store'])->name('create.store');

                        $router->get('styles', [StyleController::class, 'index'])->name('styles.index');
                        $router->post('styles/sync', [StyleController::class, 'sync'])->name('styles.sync');

                        $router->get('avatars', [AvatarController::class, 'index'])->name('avatars.index');
                        $router->post('avatars/sync', [AvatarController::class, 'sync'])->name('avatars.sync');
                        $router->post('avatars/digital-twins', [AvatarController::class, 'storeTwin'])->name('avatars.twins.store');
                        $router->post('avatars/{avatar}/consent', [AvatarController::class, 'consent'])->name('avatars.consent');
                        $router->post('avatars/{avatar}/refresh', [AvatarController::class, 'refreshTwin'])->name('avatars.refresh');

                        $router->get('voices', [VoiceController::class, 'index'])->name('voices.index');
                        $router->post('voices/sync', [VoiceController::class, 'sync'])->name('voices.sync');

                        $router->post('assets', [AssetController::class, 'store'])->name('assets.store');
                        $router->delete('assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

                        $router->get('templates', [TemplateController::class, 'index'])->name('templates.index');
                        $router->get('templates/{template}', [TemplateController::class, 'show'])->name('templates.show');
                        $router->post('templates/{template}', [TemplateController::class, 'generate'])->name('templates.generate');

                        $router->get('brand', [BrandController::class, 'index'])->name('brand.index');
                        $router->post('brand/refresh', [BrandController::class, 'refresh'])->name('brand.refresh');
                        $router->post('brand/kits', [BrandController::class, 'storeKit'])->name('brand.kits.store');
                        $router->patch('brand/kits/{kit}', [BrandController::class, 'updateKit'])->name('brand.kits.update');
                        $router->delete('brand/kits/{kit}', [BrandController::class, 'destroyKit'])->name('brand.kits.destroy');
                        $router->post('brand/glossaries', [BrandController::class, 'storeGlossary'])->name('brand.glossaries.store');

                        $router->post('script', [ScriptWriterController::class, 'store'])->name('script.generate');

                        $router->get('speech', [SpeechController::class, 'index'])->name('speech.index');
                        $router->post('speech', [SpeechController::class, 'store'])->name('speech.store');

                        $router->get('tools', [ToolsController::class, 'index'])->name('tools.index');
                        $router->post('tools/lipsync', [ToolsController::class, 'lipsync'])->name('tools.lipsync');
                        $router->post('tools/clip', [ToolsController::class, 'clip'])->name('tools.clip');
                        $router->post('tools/filler', [ToolsController::class, 'fillerWords'])->name('tools.filler');
                        $router->post('tools/translate', [ToolsController::class, 'translate'])->name('tools.translate');

                        $router->post('proofread', [ProofreadController::class, 'store'])->name('proofread.store');
                        $router->get('proofread/{video}', [ProofreadController::class, 'show'])->name('proofread.show');
                        $router->post('proofread/{video}/srt', [ProofreadController::class, 'update'])->name('proofread.update');
                        $router->post('proofread/{video}/generate', [ProofreadController::class, 'generate'])->name('proofread.generate');

                        $router->get('batches', [BatchController::class, 'index'])->name('batches.index');
                        $router->post('batches', [BatchController::class, 'store'])->name('batches.store');
                        $router->post('batches/{batch}/refresh', [BatchController::class, 'refresh'])->name('batches.refresh');

                        $router->get('hyperframes', [HyperFramesController::class, 'index'])->name('hyperframes.index');
                        $router->post('hyperframes', [HyperFramesController::class, 'store'])->name('hyperframes.store');

                        $router->prefix('sessions')->name('sessions.')->group(function (Router $router): void {
                            $router->get('{session}', [SessionController::class, 'show'])->name('show');
                            $router->get('{session}/check', [SessionController::class, 'check'])->name('check');
                            $router->post('{session}/message', [SessionController::class, 'message'])->name('message');
                            $router->post('{session}/approve', [SessionController::class, 'approve'])->name('approve');
                            $router->post('{session}/stop', [SessionController::class, 'stop'])->name('stop');
                            $router->delete('{session}', [SessionController::class, 'destroy'])->name('destroy');
                        });
                    });
            });

        return $this;
    }

    private function registerAdminRoutes(): static
    {
        $this->router()
            ->group([
                'middleware' => ['web', 'auth'],
            ], function (Router $router): void {
                $router->prefix('dashboard/admin/video-agent')
                    ->name('dashboard.admin.video-agent.')
                    ->middleware('admin')
                    ->group(function (Router $router): void {
                        $router->get('settings', [VideoAgentSettingController::class, 'index'])->name('settings');
                        $router->post('settings', [VideoAgentSettingController::class, 'update'])->name('update');
                        $router->post('webhook/register', [VideoAgentSettingController::class, 'registerWebhook'])->name('webhook.register');
                        $router->post('webhook/delete', [VideoAgentSettingController::class, 'deleteWebhook'])->name('webhook.delete');
                    });
            });

        return $this;
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }

    public function registerKey(): string
    {
        return 'video-agent';
    }

    public static function uninstall(): void {}
}
