<?php

namespace VitalSaaS\VitalAccess\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use VitalSaaS\VitalAccess\Models\AccessModule;
use VitalSaaS\VitalAccess\Models\AccessPermission;
use VitalSaaS\VitalAccess\Models\AccessRole;
use VitalSaaS\VitalAccess\Models\AccessRoleCategory;

class VitalAccessModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create role categories
        $this->createRoleCategories();

        // Create basic permissions
        $this->createPermissions();

        // Create basic roles
        $this->createRoles();

        // Create navigation modules
        $this->createModules();

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        // Assign permissions to modules
        $this->assignPermissionsToModules();

        // Create default users and assign roles
        $this->createDefaultUsersAndAssignRoles();
    }

    /**
     * Create role categories
     */
    protected function createRoleCategories(): void
    {
        $categories = [
            [
                'name' => 'Sistema',
                'slug' => 'system',
                'description' => 'Roles del sistema y administración',
                'is_system' => true,
            ],
            [
                'name' => 'Operacional',
                'slug' => 'operational',
                'description' => 'Roles operacionales y de negocio',
                'is_system' => false,
            ],
            [
                'name' => 'Usuario Final',
                'slug' => 'end-user',
                'description' => 'Roles de usuarios finales',
                'is_system' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            AccessRoleCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }
    }

    /**
     * Create basic permissions
     */
    protected function createPermissions(): void
    {
        $permissions = [
            // Dashboard permissions
            ['group' => 'dashboard', 'action' => 'view', 'name' => 'Ver Dashboard', 'slug' => 'dashboard.view'],

            // User management permissions
            ['group' => 'users', 'action' => 'view', 'name' => 'Ver Usuarios', 'slug' => 'users.view'],
            ['group' => 'users', 'action' => 'create', 'name' => 'Crear Usuarios', 'slug' => 'users.create'],
            ['group' => 'users', 'action' => 'edit', 'name' => 'Editar Usuarios', 'slug' => 'users.edit'],
            ['group' => 'users', 'action' => 'delete', 'name' => 'Eliminar Usuarios', 'slug' => 'users.delete'],

            // Role management permissions
            ['group' => 'roles', 'action' => 'view', 'name' => 'Ver Roles', 'slug' => 'roles.view'],
            ['group' => 'roles', 'action' => 'create', 'name' => 'Crear Roles', 'slug' => 'roles.create'],
            ['group' => 'roles', 'action' => 'edit', 'name' => 'Editar Roles', 'slug' => 'roles.edit'],
            ['group' => 'roles', 'action' => 'delete', 'name' => 'Eliminar Roles', 'slug' => 'roles.delete'],

            // Permission management permissions
            ['group' => 'permissions', 'action' => 'view', 'name' => 'Ver Permisos', 'slug' => 'permissions.view'],
            ['group' => 'permissions', 'action' => 'create', 'name' => 'Crear Permisos', 'slug' => 'permissions.create'],
            ['group' => 'permissions', 'action' => 'edit', 'name' => 'Editar Permisos', 'slug' => 'permissions.edit'],
            ['group' => 'permissions', 'action' => 'delete', 'name' => 'Eliminar Permisos', 'slug' => 'permissions.delete'],

            // Module management permissions
            ['group' => 'modules', 'action' => 'view', 'name' => 'Ver Módulos', 'slug' => 'modules.view'],
            ['group' => 'modules', 'action' => 'create', 'name' => 'Crear Módulos', 'slug' => 'modules.create'],
            ['group' => 'modules', 'action' => 'edit', 'name' => 'Editar Módulos', 'slug' => 'modules.edit'],
            ['group' => 'modules', 'action' => 'delete', 'name' => 'Eliminar Módulos', 'slug' => 'modules.delete'],

            // Settings permissions
            ['group' => 'settings', 'action' => 'view', 'name' => 'Ver Configuración', 'slug' => 'settings.view'],
            ['group' => 'settings', 'action' => 'edit', 'name' => 'Editar Configuración', 'slug' => 'settings.edit'],

            // Reports permissions
            ['group' => 'reports', 'action' => 'view', 'name' => 'Ver Reportes', 'slug' => 'reports.view'],
            ['group' => 'reports', 'action' => 'export', 'name' => 'Exportar Reportes', 'slug' => 'reports.export'],

            // Profile permissions
            ['group' => 'profile', 'action' => 'view', 'name' => 'Ver Perfil', 'slug' => 'profile.view'],
            ['group' => 'profile', 'action' => 'edit', 'name' => 'Editar Perfil', 'slug' => 'profile.edit'],
        ];

        foreach ($permissions as $permissionData) {
            AccessPermission::firstOrCreate(
                ['slug' => $permissionData['slug']],
                array_merge($permissionData, [
                    'description' => "Permite {$permissionData['name']}",
                    'is_system' => true,
                ])
            );
        }
    }

    /**
     * Create basic roles
     */
    protected function createRoles(): void
    {
        $systemCategory = AccessRoleCategory::where('slug', 'system')->first();
        $operationalCategory = AccessRoleCategory::where('slug', 'operational')->first();
        $endUserCategory = AccessRoleCategory::where('slug', 'end-user')->first();

        $roles = [
            [
                'name' => 'Super Administrador',
                'slug' => 'superadmin',
                'description' => 'Acceso total al sistema',
                'category_id' => $systemCategory->id,
                'is_system' => true,
                'level' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Administrador del sistema con acceso a la mayoría de funciones',
                'category_id' => $systemCategory->id,
                'is_system' => true,
                'level' => 80,
                'is_active' => true,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Gestión operacional y supervisión',
                'category_id' => $operationalCategory->id,
                'is_system' => false,
                'level' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Creación y edición de contenido',
                'category_id' => $operationalCategory->id,
                'is_system' => false,
                'level' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Usuario',
                'slug' => 'user',
                'description' => 'Usuario estándar del sistema',
                'category_id' => $endUserCategory->id,
                'is_system' => false,
                'level' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            AccessRole::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }

    /**
     * Create navigation modules
     */
    protected function createModules(): void
    {
        // Dashboard module
        $dashboard = AccessModule::firstOrCreate(
            ['slug' => 'dashboard'],
            [
                'name' => 'Dashboard',
                'icon' => 'heroicon-o-home',
                'route' => 'dashboard',
                'type' => 'menu',
                'sort_order' => 1,
                'depth' => 0,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // Administration module (parent)
        $admin = AccessModule::firstOrCreate(
            ['slug' => 'administration'],
            [
                'name' => 'Administración',
                'icon' => 'heroicon-o-cog-6-tooth',
                'type' => 'group',
                'sort_order' => 2,
                'depth' => 0,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // Users sub-module
        AccessModule::firstOrCreate(
            ['slug' => 'users'],
            [
                'parent_id' => $admin->id,
                'name' => 'Usuarios',
                'icon' => 'heroicon-o-users',
                'route' => null, // Will use Filament's auto-generated User resource
                'type' => 'menu',
                'sort_order' => 1,
                'depth' => 1,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // Roles sub-module (VitalAccess Filament Resource)
        AccessModule::firstOrCreate(
            ['slug' => 'access-roles'],
            [
                'parent_id' => $admin->id,
                'name' => 'Roles',
                'icon' => 'heroicon-o-shield-check',
                'route' => null, // Auto-handled by VitalAccess Filament Resource
                'type' => 'menu',
                'sort_order' => 2,
                'depth' => 1,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // Permissions sub-module (VitalAccess Filament Resource)
        AccessModule::firstOrCreate(
            ['slug' => 'access-permissions'],
            [
                'parent_id' => $admin->id,
                'name' => 'Permisos',
                'icon' => 'heroicon-o-key',
                'route' => null, // Auto-handled by VitalAccess Filament Resource
                'type' => 'menu',
                'sort_order' => 3,
                'depth' => 1,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // Modules sub-module (VitalAccess Filament Resource)
        AccessModule::firstOrCreate(
            ['slug' => 'access-modules'],
            [
                'parent_id' => $admin->id,
                'name' => 'Módulos',
                'icon' => 'heroicon-o-squares-2x2',
                'route' => null, // Auto-handled by VitalAccess Filament Resource
                'type' => 'menu',
                'sort_order' => 4,
                'depth' => 1,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // Reports module (parent)
        $reports = AccessModule::firstOrCreate(
            ['slug' => 'reports'],
            [
                'name' => 'Reportes',
                'icon' => 'heroicon-o-chart-bar',
                'type' => 'group',
                'sort_order' => 3,
                'depth' => 0,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // System reports sub-module
        AccessModule::firstOrCreate(
            ['slug' => 'system-reports'],
            [
                'parent_id' => $reports->id,
                'name' => 'Reportes del Sistema',
                'icon' => 'heroicon-o-document-chart-bar',
                'route' => 'reports.system',
                'type' => 'menu',
                'sort_order' => 1,
                'depth' => 1,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // User reports sub-module
        AccessModule::firstOrCreate(
            ['slug' => 'user-reports'],
            [
                'parent_id' => $reports->id,
                'name' => 'Reportes de Usuarios',
                'icon' => 'heroicon-o-user-group',
                'route' => 'reports.users',
                'type' => 'menu',
                'sort_order' => 2,
                'depth' => 1,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // Settings module
        AccessModule::firstOrCreate(
            ['slug' => 'settings'],
            [
                'name' => 'Configuración',
                'icon' => 'heroicon-o-cog-8-tooth',
                'route' => 'admin.settings.index',
                'type' => 'menu',
                'sort_order' => 4,
                'depth' => 0,
                'is_active' => true,
                'is_visible' => true,
            ]
        );

        // Profile module
        AccessModule::firstOrCreate(
            ['slug' => 'profile'],
            [
                'name' => 'Mi Perfil',
                'icon' => 'heroicon-o-user-circle',
                'route' => 'profile.edit',
                'type' => 'menu',
                'sort_order' => 5,
                'depth' => 0,
                'is_active' => true,
                'is_visible' => true,
            ]
        );
    }

    /**
     * Assign permissions to roles
     */
    protected function assignPermissionsToRoles(): void
    {
        // Get roles
        $superadmin = AccessRole::where('slug', 'superadmin')->first();
        $admin = AccessRole::where('slug', 'admin')->first();
        $manager = AccessRole::where('slug', 'manager')->first();
        $editor = AccessRole::where('slug', 'editor')->first();
        $user = AccessRole::where('slug', 'user')->first();

        // Superadmin gets all permissions
        $allPermissions = AccessPermission::all();
        $superadmin->permissions()->sync($allPermissions->pluck('id')->toArray());

        // Admin gets most permissions except user management
        $adminPermissions = AccessPermission::whereNotIn('group', ['users'])->get();
        $admin->permissions()->sync($adminPermissions->pluck('id')->toArray());

        // Manager gets operational permissions
        $managerPermissions = AccessPermission::whereIn('group', ['dashboard', 'reports', 'profile'])->get();
        $manager->permissions()->sync($managerPermissions->pluck('id')->toArray());

        // Editor gets content permissions
        $editorPermissions = AccessPermission::whereIn('group', ['dashboard', 'profile'])->get();
        $editor->permissions()->sync($editorPermissions->pluck('id')->toArray());

        // User gets basic permissions
        $userPermissions = AccessPermission::whereIn('slug', ['dashboard.view', 'profile.view', 'profile.edit'])->get();
        $user->permissions()->sync($userPermissions->pluck('id')->toArray());
    }

    /**
     * Assign permissions to modules
     */
    protected function assignPermissionsToModules(): void
    {
        $permissionModuleMap = [
            'dashboard' => ['dashboard.view'],
            'users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
            'roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
            'permissions' => ['permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'],
            'modules' => ['modules.view', 'modules.create', 'modules.edit', 'modules.delete'],
            'settings' => ['settings.view', 'settings.edit'],
            'system-reports' => ['reports.view', 'reports.export'],
            'user-reports' => ['reports.view', 'reports.export'],
            'profile' => ['profile.view', 'profile.edit'],
        ];

        foreach ($permissionModuleMap as $moduleSlug => $permissionSlugs) {
            $module = AccessModule::where('slug', $moduleSlug)->first();
            if ($module) {
                $permissions = AccessPermission::whereIn('slug', $permissionSlugs)->get();
                $module->permissions()->sync($permissions->pluck('id')->toArray());
            }
        }
    }

    /**
     * Create default users and assign roles
     */
    protected function createDefaultUsersAndAssignRoles(): void
    {
        // Get User model dynamically
        $userModel = config('vitalaccess.user_model', 'App\\Models\\User');

        // Create default Super Admin user if not exists
        $superAdmin = $userModel::firstOrCreate(
            ['email' => 'superadmin@vitalaccess.test'],
            [
                'name' => 'Super Administrator',
                'email_verified_at' => now(),
                'password' => bcrypt('superadmin123'),
            ]
        );

        // Create default Admin user if not exists
        $admin = $userModel::firstOrCreate(
            ['email' => 'admin@vitalaccess.test'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => bcrypt('admin123'),
            ]
        );

        // Create default Manager user if not exists
        $manager = $userModel::firstOrCreate(
            ['email' => 'manager@vitalaccess.test'],
            [
                'name' => 'Manager User',
                'email_verified_at' => now(),
                'password' => bcrypt('manager123'),
            ]
        );

        // Get roles
        $superAdminRole = AccessRole::where('slug', 'superadmin')->first();
        $adminRole = AccessRole::where('slug', 'admin')->first();
        $managerRole = AccessRole::where('slug', 'manager')->first();

        // Assign roles to users using the access_user_roles table
        if ($superAdminRole) {
            $this->assignRoleToUser($superAdmin->id, $superAdminRole->id);
        }

        if ($adminRole) {
            $this->assignRoleToUser($admin->id, $adminRole->id);
        }

        if ($managerRole) {
            $this->assignRoleToUser($manager->id, $managerRole->id);
        }

        $this->command->info('✅ Usuarios por defecto creados y roles asignados:');
        $this->command->info('   - Super Admin: superadmin@vitalaccess.test / superadmin123');
        $this->command->info('   - Admin: admin@vitalaccess.test / admin123');
        $this->command->info('   - Manager: manager@vitalaccess.test / manager123');
    }

    /**
     * Assign role to user in access_user_roles table
     */
    private function assignRoleToUser(string $userId, string $roleId): void
    {
        // Check if assignment already exists
        $exists = \DB::table('access_user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->exists();

        if (!$exists) {
            \DB::table('access_user_roles')->insert([
                'id' => \Str::uuid(),
                'user_id' => $userId,
                'role_id' => $roleId,
                'granted_at' => now(),
                'granted_by' => $userId, // Self-assigned during seeding
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}