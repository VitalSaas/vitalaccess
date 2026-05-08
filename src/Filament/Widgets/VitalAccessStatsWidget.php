<?php

namespace VitalSaaS\VitalAccess\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use VitalSaaS\VitalAccess\Models\AccessRole;
use VitalSaaS\VitalAccess\Models\AccessPermission;
use VitalSaaS\VitalAccess\Models\AccessModule;

class VitalAccessStatsWidget extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $userModel = config('vitalaccess.user_model', 'App\\Models\\User');

        return [
            Stat::make('Total Users', $this->getUsersCount())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Active Roles', $this->getActiveRolesCount())
                ->description('Available roles')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),

            Stat::make('Permissions', $this->getPermissionsCount())
                ->description('System permissions')
                ->descriptionIcon('heroicon-m-key')
                ->color('warning'),

            Stat::make('Navigation Modules', $this->getActiveModulesCount())
                ->description('Active navigation items')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info'),
        ];
    }

    private function getUsersCount(): int
    {
        try {
            $userModel = config('vitalaccess.user_model', 'App\\Models\\User');
            return $userModel::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getActiveRolesCount(): int
    {
        try {
            return AccessRole::where('is_active', true)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getPermissionsCount(): int
    {
        try {
            return AccessPermission::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getActiveModulesCount(): int
    {
        try {
            return AccessModule::where('is_active', true)
                ->where('is_visible', true)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
