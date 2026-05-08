<?php

namespace VitalSaaS\VitalAccess\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class VitalAccessWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Get VitalAccess statistics
        $totalUsers = $this->getUsersCount();
        $totalRoles = $this->getRolesCount();
        $totalPermissions = $this->getPermissionsCount();
        $totalModules = $this->getModulesCount();

        return [
            Stat::make('Usuarios', $totalUsers)
                ->description('Total de usuarios en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->url('/admin/users'),

            Stat::make('Roles', $totalRoles)
                ->description('Roles configurados')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success')
                ->url('/admin/access-roles'),

            Stat::make('Permisos', $totalPermissions)
                ->description('Permisos del sistema')
                ->descriptionIcon('heroicon-m-key')
                ->color('warning')
                ->url('/admin/access-permissions'),

            Stat::make('Módulos', $totalModules)
                ->description('Módulos VitalAccess')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info')
                ->url('/admin/access-modules'),
        ];
    }

    protected function getUsersCount(): int
    {
        try {
            return DB::table('users')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getRolesCount(): int
    {
        try {
            return DB::table('access_roles')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getPermissionsCount(): int
    {
        try {
            return DB::table('access_permissions')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getModulesCount(): int
    {
        try {
            return DB::table('access_modules')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}