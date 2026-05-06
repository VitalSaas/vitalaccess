<?php

namespace VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource;

class ListAccessPermissions extends ListRecords
{
    protected static string $resource = AccessPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
