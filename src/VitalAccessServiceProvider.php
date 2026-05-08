<?php

namespace VitalSaaS\VitalAccess;

use Illuminate\Support\ServiceProvider;
use VitalSaaS\VitalAccess\Commands\InstallVitalAccessCommand;
use VitalSaaS\VitalAccess\Commands\PublishFilamentResourcesCommand;
use VitalSaaS\VitalAccess\Commands\VitalAccessMaintenanceCommand;
use VitalSaaS\VitalAccess\Commands\CreateUserResourceCommand;
use VitalSaaS\VitalAccess\Commands\ConfigurePanelCommand;

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

        // Publishing configuration
        $this->publishes([
            __DIR__ . '/../config/vitalaccess.php' => config_path('vitalaccess.php'),
        ], 'vitalaccess-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'vitalaccess-migrations');

        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'vitalaccess-seeders');

        // Publish Models (required for project)
        $this->publishes([
            __DIR__ . '/Models' => app_path('Models'),
        ], 'vitalaccess-models');

        // Publish Traits (required for project)
        $this->publishes([
            __DIR__ . '/Traits' => app_path('Traits'),
        ], 'vitalaccess-traits');

        // Publish Middleware (required for project)
        $this->publishes([
            __DIR__ . '/Middleware' => app_path('Http/Middleware'),
        ], 'vitalaccess-middleware');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallVitalAccessCommand::class,
                PublishFilamentResourcesCommand::class,
                VitalAccessMaintenanceCommand::class,
                CreateUserResourceCommand::class,
                ConfigurePanelCommand::class,
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