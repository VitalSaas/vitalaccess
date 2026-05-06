<?php

namespace VitalSaaS\VitalAccess;

use Illuminate\Support\ServiceProvider;
use VitalSaaS\VitalAccess\Commands\InstallVitalAccessCommand;
use VitalSaaS\VitalAccess\Commands\PublishFilamentResourcesCommand;
use VitalSaaS\VitalAccess\Commands\VitalAccessMaintenanceCommand;

class VitalAccessServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/vitalaccess.php', 'vitalaccess');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'vitalaccess');

        // Register middleware
        $this->registerMiddleware();

        $this->publishes([
            __DIR__ . '/../config/vitalaccess.php' => config_path('vitalaccess.php'),
        ], 'vitalaccess-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'vitalaccess-migrations');

        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'vitalaccess-seeders');

        // Publish Filament Resources
        $this->publishes([
            __DIR__ . '/Filament/Resources' => app_path('Filament/Resources'),
        ], 'vitalaccess-filament-resources');

        // Publish Filament Widgets
        $this->publishes([
            __DIR__ . '/Filament/Widgets' => app_path('Filament/Widgets'),
        ], 'vitalaccess-filament-widgets');

        // Publish Filament Pages
        $this->publishes([
            __DIR__ . '/Filament/Pages' => app_path('Filament/Pages'),
        ], 'vitalaccess-filament-pages');

        // Publish Models
        $this->publishes([
            __DIR__ . '/Models' => app_path('Models'),
        ], 'vitalaccess-models');

        // Publish Traits
        $this->publishes([
            __DIR__ . '/Traits' => app_path('Traits'),
        ], 'vitalaccess-traits');

        // Publish Middleware
        $this->publishes([
            __DIR__ . '/Middleware' => app_path('Http/Middleware'),
        ], 'vitalaccess-middleware');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallVitalAccessCommand::class,
                PublishFilamentResourcesCommand::class,
                VitalAccessMaintenanceCommand::class,
            ]);
        }
    }

    /**
     * Register VitalAccess middleware
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        $router->aliasMiddleware('vitalaccess', \VitalSaaS\VitalAccess\Middleware\VitalAccessMiddleware::class);
    }
}