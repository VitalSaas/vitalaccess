<?php

namespace VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource;

class EditAccessPermission extends EditRecord
{
    protected static string $resource = AccessPermissionResource::class;

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
