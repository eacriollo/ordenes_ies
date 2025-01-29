<?php

namespace App\Filament\Resources\PersonaResource\Pages;

use App\Filament\Resources\PersonaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonas extends ListRecords
{
    protected static string $resource = PersonaResource::class;
}
