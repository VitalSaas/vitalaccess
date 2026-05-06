<?php

namespace VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource;

class CreateAccessRole extends CreateRecord
{
    protected static string $resource = AccessRoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
