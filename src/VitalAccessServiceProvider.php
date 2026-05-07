<?php

namespace VitalSaaS\VitalAccess;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Panel;
use VitalSaaS\VitalAccess\Commands\InstallVitalAccessCommand;
use VitalSaaS\VitalAccess\Commands\PublishFilamentResourcesCommand;
use VitalSaaS\VitalAccess\Commands\VitalAccessMaintenanceCommand;
use VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource;
use VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource;
use VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource;

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
        // $this->loadRoutesFrom(__DIR__ . '/../routes/web.php'); // Comentado: VitalAccessController no existe
        // $this->loadRoutesFrom(__DIR__ . '/../routes/api.php'); // Comentado: puede tener dependencias faltantes

        // Register middleware
        $this->registerMiddleware();

        // Auto-register Filament resources
        $this->registerFilamentResources();

        $this->publishes([
            __DIR__ . '/../config/vitalaccess.php' => config_path('vitalaccess.php'),
        ], 'vitalaccess-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'vitalaccess-migrations');

        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'vitalaccess-seeders');

        // Optional: Publish Filament Resources (only if user wants to customize)
        $this->publishes([
            __DIR__ . '/Filament/Resources' => app_path('Filament/Resources'),
        ], 'vitalaccess-filament-resources-custom');

        // Optional: Publish Filament Widgets
        $this->publishes([
            __DIR__ . '/Filament/Widgets' => app_path('Filament/Widgets'),
        ], 'vitalaccess-filament-widgets-custom');

        // Optional: Publish Filament Pages
        $this->publishes([
            __DIR__ . '/Filament/Pages' => app_path('Filament/Pages'),
        ], 'vitalaccess-filament-pages-custom');

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

    /**
     * Auto-register Filament resources for plug-and-play functionality
     */
    protected function registerFilamentResources(): void
    {
        // Only register if Filament is available
        if (class_exists(\Filament\Facades\Filament::class)) {
            // Use a different approach - hook into panel registration event
            $this->app->resolving(\Filament\Panel::class, function (\Filament\Panel $panel) {
                // Only register for admin panel (or default panel)
                if ($panel->getId() === 'admin' || $panel->isDefault()) {
                    $panel->discoverResources(
                        in: __DIR__ . '/Filament/Resources',
                        for: 'VitalSaaS\\VitalAccess\\Filament\\Resources'
                    );
                }
            });
        }
    }
}