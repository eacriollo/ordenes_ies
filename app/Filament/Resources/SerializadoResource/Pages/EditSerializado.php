<?php

namespace App\Filament\Resources\SerializadoResource\Pages;

use App\Filament\Resources\SerializadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSerializado extends EditRecord
{
    protected static string $resource = SerializadoResource::class;

    protected function  getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
