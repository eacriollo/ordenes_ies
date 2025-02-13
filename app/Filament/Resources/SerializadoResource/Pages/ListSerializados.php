<?php

namespace App\Filament\Resources\SerializadoResource\Pages;

use App\Filament\Resources\SerializadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSerializados extends ListRecords
{
    protected static string $resource = SerializadoResource::class;

    protected function  getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
