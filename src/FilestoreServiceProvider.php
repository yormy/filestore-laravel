<?php

namespace Yormy\FilestoreLaravel;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Yormy\FilestoreLaravel\Console\Commands\VerifySetup;
use Yormy\FilestoreLaravel\ServiceProviders\EventServiceProvider;
use Yormy\FilestoreLaravel\ServiceProviders\RouteServiceProvider;

class FilestoreServiceProvider extends ServiceProvider
{
    const CONFIG_FILE = __DIR__.'/../config/filestore.php';

    const CONFIG_FILE_CHUNKED = __DIR__.'/../config/chunk-upload.php';

    const CONFIG_IDE_HELPER_FILE = __DIR__.'/../config/ide-helper.php';

    /**
     * @psalm-suppress MissingReturnType
     */
    public function boot(Router $router)
    {
        $this->publish();

        $this->registerCommands();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->registerTranslations();

        $this->morphMaps();
    }

    /**
     * @psalm-suppress MixedArgument
     */
    public function register()
    {
        $this->mergeConfigFrom(static::CONFIG_FILE, 'filestore');
        $this->mergeConfigFrom(static::CONFIG_FILE_CHUNKED, 'chunk-upload');
        $this->mergeConfigFrom(static::CONFIG_IDE_HELPER_FILE, 'ide-helper');

        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    private function publish(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_FILE => config_path('filestore.php'),
                self::CONFIG_FILE_CHUNKED => config_path('chunk-upload.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'migrations');

            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/filestore'),
            ], 'translations');
        }
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                VerifySetup::class,
            ]);
        }
    }

    public function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filestore');
    }

    private function morphMaps(): void {}
}
