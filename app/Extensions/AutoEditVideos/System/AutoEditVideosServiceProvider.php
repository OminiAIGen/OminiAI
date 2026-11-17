<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System;

use App\Domains\Marketplace\Contracts\ExtensionRegisterKeyProviderInterface;
use App\Domains\Marketplace\Contracts\UninstallExtensionServiceProviderInterface;
use App\Extensions\AutoEditVideos\System\Console\Commands\AutoEditVideosPollCommand;
use App\Extensions\AutoEditVideos\System\Http\Controllers\AutoEditVideosController;
use App\Extensions\AutoEditVideos\System\Http\Controllers\AutoEditVideosSettingController;
use App\Extensions\AutoEditVideos\System\Http\Controllers\SessionController;
use App\Extensions\AutoEditVideos\System\Http\Controllers\SourceController;
use App\Extensions\AutoEditVideos\System\Services\Rendley\RendleyClient;
use App\Http\Middleware\CheckTemplateTypeAndPlan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AutoEditVideosServiceProvider extends ServiceProvider implements ExtensionRegisterKeyProviderInterface, UninstallExtensionServiceProviderInterface
{
    public function register(): void
    {
        $this->registerConfig();

        $this->app->bind(RendleyClient::class, static fn (): RendleyClient => new RendleyClient);
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
        $this->mergeConfigFrom(__DIR__ . '/../config/auto-edit-videos.php', 'auto-edit-videos');

        return $this;
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'auto-edit-videos');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'auto-edit-videos');

        return $this;
    }

    public function registerMigrations(): static
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        return $this;
    }

    /**
     * Rendley publishes no webhooks, so a scheduled sweep is the only thing
     * that can rescue an edit whose queued poll job never ran.
     */
    public function registerCommands(): static
    {
        if (! $this->app->runningInConsole()) {
            return $this;
        }

        $this->commands([AutoEditVideosPollCommand::class]);

        $this->app->booted(function (): void {
            $this->app->make(Schedule::class)
                ->command('auto-edit-videos:poll')
                ->everyMinute()
                ->withoutOverlapping();
        });

        return $this;
    }

    public function publishAssets(): static
    {
        $this->publishes([
            __DIR__ . '/../resources/assets/images' => public_path('vendor/auto-edit-videos/images'),
        ], 'extension');

        return $this;
    }

    private function registerRoutes(): static
    {
        $this->registerUserRoutes()
            ->registerAdminRoutes();

        return $this;
    }

    private function registerUserRoutes(): static
    {
        $this->router()
            ->group([
                'middleware' => ['web', 'auth'],
            ], function (Router $router): void {
                $router->prefix('dashboard/user/auto-edit-videos')
                    ->name('dashboard.user.auto-edit-videos.')
                    ->middleware(CheckTemplateTypeAndPlan::class)
                    ->group(function (Router $router): void {
                        $router->get('/', [AutoEditVideosController::class, 'index'])->name('index');
                        $router->post('/', [AutoEditVideosController::class, 'store'])->name('store')->middleware('throttle:20,1');
                        $router->get('library', [AutoEditVideosController::class, 'library'])->name('library');
                        $router->get('check', [AutoEditVideosController::class, 'check'])->name('check')->middleware('throttle:60,1');
                        $router->post('bulk-delete', [AutoEditVideosController::class, 'bulkDestroy'])->name('bulk-delete');

                        $router->post('sources', [SourceController::class, 'store'])->name('sources.store')->middleware('throttle:30,1');
                        $router->delete('sources/{source}', [SourceController::class, 'destroy'])->name('sources.destroy');

                        $router->prefix('sessions')->name('sessions.')->group(function (Router $router): void {
                            $router->get('{session}', [SessionController::class, 'show'])->name('show');
                            $router->get('{session}/check', [SessionController::class, 'check'])->name('check')->middleware('throttle:60,1');
                            $router->get('{session}/cost', [SessionController::class, 'cost'])->name('cost')->middleware('throttle:30,1');
                            $router->post('{session}/message', [SessionController::class, 'message'])->name('message')->middleware('throttle:10,1');
                            $router->post('{session}/export', [SessionController::class, 'export'])->name('export')->middleware('throttle:20,1');
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
                $router->prefix('dashboard/admin/auto-edit-videos')
                    ->name('dashboard.admin.auto-edit-videos.')
                    ->middleware('admin')
                    ->group(function (Router $router): void {
                        $router->get('settings', [AutoEditVideosSettingController::class, 'index'])->name('settings');
                        $router->post('settings', [AutoEditVideosSettingController::class, 'update'])->name('update');
                        $router->post('settings/test', [AutoEditVideosSettingController::class, 'testConnection'])->name('test');
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
        return 'auto-edit-videos';
    }

    public static function uninstall(): void {}
}
