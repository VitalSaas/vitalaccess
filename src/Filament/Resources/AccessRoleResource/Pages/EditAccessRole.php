<?php

namespace VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource;

class EditAccessRole extends EditRecord
{
    protected static string $resource = AccessRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
