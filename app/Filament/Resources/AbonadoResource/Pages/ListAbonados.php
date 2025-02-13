<?php

namespace App\Filament\Resources\AbonadoResource\Pages;

use App\Filament\Resources\AbonadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbonados extends ListRecords
{
    protected static string $resource = AbonadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nuevo Abonado'),
        ];
    }
}
