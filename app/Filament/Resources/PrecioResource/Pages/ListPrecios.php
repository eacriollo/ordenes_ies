<?php

namespace App\Filament\Resources\PrecioResource\Pages;

use App\Filament\Resources\PrecioResource;
use App\Imports\equiposNuevosImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListPrecios extends ListRecords
{
    protected static string $resource = PrecioResource::class;

    protected function  getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),


        ];
    }
}
