<?php

namespace VitalSaaS\VitalAccess\Commands;

use Illuminate\Console\Command;
use VitalSaaS\VitalAccess\Models\AccessRole;
use VitalSaaS\VitalAccess\Models\AccessPermission;
use VitalSaaS\VitalAccess\Models\AccessModule;

class VitalAccessMaintenanceCommand extends Command
{
    protected $signature = 'vitalaccess:maintenance
                           {--sync : Sync permissions with modules}
                           {--cleanup : Clean unused permissions}
                           {--stats : Show system statistics}';

    protected $description = 'VitalAccess maintenance and utilities';

    public function handle(): int
    {
        $this->info('🔧 VitalAccess Maintenance Tool');
        $this->newLine();

        if ($this->option('stats') || !$this->hasOptions()) {
            $this->showStats();
        }

        if ($this->option('sync')) {
            $this->syncPermissions();
        }

        if ($this->option('cleanup')) {
            $this->cleanupUnusedPermissions();
        }

        return Command::SUCCESS;
    }

    protected function showStats(): void
    {
        $this->info('📊 System Statistics');
        $this->newLine();

        try {
            $userModel = config('vitalaccess.user_model', 'App\\Models\\User');
            $usersCount = $userModel::count();
            $rolesCount = AccessRole::count();
            $activeRolesCount = AccessRole::where('is_active', true)->count();
            $permissionsCount = AccessPermission::count();
            $modulesCount = AccessModule::count();
            $activeModulesCount = AccessModule::where('is_active', true)->where('is_visible', true)->count();

            $this->table(['Metric', 'Count'], [
                ['Total Users', $usersCount],
                ['Total Roles', $rolesCount],
                ['Active Roles', $activeRolesCount],
                ['Total Permissions', $permissionsCount],
                ['Total Modules', $modulesCount],
                ['Active Modules', $activeModulesCount],
            ]);

        } catch (\Exception $e) {
            $this->error('Failed to get statistics: ' . $e->getMessage());
        }
    }

    protected function syncPermissions(): void
    {
        $this->info('🔄 Syncing permissions with modules...');

        try {
            $modules = AccessModule::where('is_active', true)->get();
            $synced = 0;

            foreach ($modules as $module) {
                // Create default permissions for each module if they don't exist
                $permissionSlugs = [
                    $module->slug . '.view',
                    $module->slug . '.create',
                    $module->slug . '.edit',
                    $module->slug . '.delete',
                ];

                foreach ($permissionSlugs as $slug) {
                    $permission = AccessPermission::firstOrCreate([
                        'slug' => $slug,
                    ], [
                        'name' => ucfirst(str_replace(['.', '_'], ' ', $slug)),
                        'group' => $module->slug,
                        'action' => explode('.', $slug)[1] ?? 'access',
                        'description' => 'Auto-generated permission for ' . $module->name,
                        'is_system' => false,
                    ]);

                    // Attach permission to module if not already attached
                    if (!$module->permissions()->where('permission_id', $permission->id)->exists()) {
                        $module->permissions()->attach($permission->id);
                        $synced++;
                    }
                }
            }

            $this->line("   ✅ Synced {$synced} permission-module relationships");

        } catch (\Exception $e) {
            $this->error('Failed to sync permissions: ' . $e->getMessage());
        }
    }

    protected function cleanupUnusedPermissions(): void
    {
        $this->info('🧹 Cleaning up unused permissions...');

        try {
            // Find permissions not assigned to any role or module
            $unusedPermissions = AccessPermission::whereDoesntHave('roles')
                ->whereDoesntHave('modules')
                ->where('is_system', false)
                ->get();

            if ($unusedPermissions->count() > 0) {
                $delete = $this->confirm("Found {$unusedPermissions->count()} unused permissions. Delete them?");

                if ($delete) {
                    $deleted = $unusedPermissions->count();
                    $unusedPermissions->each->delete();
                    $this->line("   ✅ Deleted {$deleted} unused permissions");
                } else {
                    $this->line("   ⏭️  Skipped cleanup");
                }
            } else {
                $this->line("   ✅ No unused permissions found");
            }

        } catch (\Exception $e) {
            $this->error('Failed to cleanup permissions: ' . $e->getMessage());
        }
    }

    protected function hasOptions(): bool
    {
        return $this->option('sync') || $this->option('cleanup') || $this->option('stats');
    }
}
