<?php

namespace VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource;

class CreateAccessModule extends CreateRecord
{
    protected static string $resource = AccessModuleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
