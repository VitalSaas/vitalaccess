<?php

namespace VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource;

class ListAccessRoles extends ListRecords
{
    protected static string $resource = AccessRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
