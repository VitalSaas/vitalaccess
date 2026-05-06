<?php

namespace VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource;

class EditAccessModule extends EditRecord
{
    protected static string $resource = AccessModuleResource::class;

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
