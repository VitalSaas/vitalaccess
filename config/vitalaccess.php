<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | This value is the prefix for VitalAccess database tables.
    | You can change it to avoid conflicts with existing tables.
    |
    */
    'table_prefix' => env('VITALACCESS_TABLE_PREFIX', 'access_'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for better performance.
    |
    */
    'cache' => [
        'enabled' => env('VITALACCESS_CACHE_ENABLED', true),
        'ttl' => env('VITALACCESS_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'vitalaccess:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Filament admin panel integration.
    |
    */
    'filament' => [
        'enabled' => true,
        'navigation_group' => 'VitalAccess',
        'navigation_sort' => 100,
        'resources' => [
            'enabled' => true,
            'auto_register' => true,
        ],
        'widgets' => [
            'enabled' => true,
            'dashboard_stats' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Configuration
    |--------------------------------------------------------------------------
    |
    | Default role configurations and settings.
    |
    */
    'roles' => [
        'default_category' => 'end-user',
        'super_admin' => 'superadmin',
        'admin' => 'admin',
        'user' => 'user',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Configuration
    |--------------------------------------------------------------------------
    |
    | Permission system configuration.
    |
    */
    'permissions' => [
        'auto_discovery' => true,
        'cache_permissions' => true,
        'groups' => [
            'dashboard' => 'Dashboard',
            'users' => 'Usuarios',
            'roles' => 'Roles',
            'permissions' => 'Permisos',
            'modules' => 'Módulos',
            'reports' => 'Reportes',
            'settings' => 'Configuración',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Configuration
    |--------------------------------------------------------------------------
    |
    | Navigation module settings.
    |
    */
    'modules' => [
        'dynamic_navigation' => true,
        'max_depth' => 3,
        'cache_navigation' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The user model that will be used by VitalAccess.
    |
    */
    'user_model' => env('VITALACCESS_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware configuration for VitalAccess.
    |
    */
    'middleware' => [
        'permission' => \VitalSaaS\VitalAccess\Middleware\VitalAccessMiddleware::class,
        'auto_register' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Installation Settings
    |--------------------------------------------------------------------------
    |
    | Settings for the installation command.
    |
    */
    'installation' => [
        'create_admin_user' => true,
        'admin_email' => env('VITALACCESS_ADMIN_EMAIL', 'admin@vitalaccess.com'),
        'admin_password' => env('VITALACCESS_ADMIN_PASSWORD', 'password'),
        'run_seeders' => true,
    ],
];
