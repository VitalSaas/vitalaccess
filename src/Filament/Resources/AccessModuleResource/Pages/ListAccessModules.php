<?php

namespace VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource;

class ListAccessModules extends ListRecords
{
    protected static string $resource = AccessModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
