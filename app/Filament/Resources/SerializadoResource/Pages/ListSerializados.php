<?php

namespace App\Filament\Resources\SerializadoResource\Pages;

use App\Filament\Resources\SerializadoResource;
use App\Imports\equiposDevueltosImport;
use App\Imports\equiposNuevosImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Support\Enums\ActionSize;

class ListSerializados extends ListRecords
{
    protected static string $resource = SerializadoResource::class;

    public $equiposNoEncontrados = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nuevo Equipo'),

            Actions\ActionGroup::make([
                Action::make('importar')
                    ->label('Equipos Nuevos')
                    ->color('info')
                    ->form([
                        FileUpload::make('file')
                            ->label('Subir archivo Excel')
                            ->disk('local')
                            ->directory('imports')
                            // ->acceptedFileTypes()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {


                            if (!isset($data['file']) || empty($data['file'])) {
                                throw new \Exception("No se subió ningún archivo.");
                            }

                            $fileName = basename($data['file']);

                            $filePath = Storage::disk('local')->path("imports/{$fileName}");

                            Excel::import(new equiposNuevosImport(), $filePath);

                            Storage::disk('local')->delete("imports/{$fileName}");

                            Notification::make()
                                ->title('Importación Exitosa')
                                ->body('Los equipos han sido importados correctamente.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error en la importación')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('devuelto')
                    ->label('Equipos Devueltos')
                    ->color('info')
                    ->form([
                        FileUpload::make('file')
                            ->label('Subir archivo Excel')
                            ->disk('local')
                            ->directory('imports')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            if (!isset($data['file']) || empty($data['file'])) {
                                throw new \Exception("No se subió ningún archivo.");
                            }

                            $fileName = basename($data['file']);

                            $filePath = Storage::disk('local')->path("imports/{$fileName}");

                            Excel::import(new equiposDevueltosImport(), $filePath);

                            Storage::disk('local')->delete("imports/{$fileName}");

                            $this->equiposNoEncontrados = equiposDevueltosImport::getEquiposNoEncontrados();

                            Notification::make()
                                ->title($this->equiposNoEncontrados ? 'Importación Exitosa con errores' : 'Importación Exitosa')
                                ->body($this->equiposNoEncontrados
                                    ? 'Algunos equipos no se encontraron en la base de datos: ' . implode(', ', $this->equiposNoEncontrados)
                                    : 'Todos los equipos han sido importados correctamente.'
                                )
                                ->success()
                                ->persistent()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error en la importación')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

            ])
                ->label('Importar')
                ->icon('heroicon-c-document-arrow-up')
                ->size(ActionSize::Small)
                ->button(),


        ];
    }
}
