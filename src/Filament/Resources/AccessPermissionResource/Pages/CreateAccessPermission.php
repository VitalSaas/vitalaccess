<?php

namespace VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource;

class CreateAccessPermission extends CreateRecord
{
    protected static string $resource = AccessPermissionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
