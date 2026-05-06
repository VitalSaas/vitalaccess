<?php

namespace VitalSaaS\VitalAccess;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource;
use VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource;
use VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource;

class VitalAccessPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                AccessRoleResource::class,
                AccessPermissionResource::class,
                AccessModuleResource::class,
            ])
            ->middleware([
                'vitalaccess',
            ])
            ->brandName('VitalAccess Admin');
    }
}