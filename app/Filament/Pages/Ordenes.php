<?php

namespace App\Filament\Pages;

//use Filament\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
//use Filament\Actions\EditAction;
use Filament\Pages\Page;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use App\Models\Ordene;

class Ordenes extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.ordenes';

    public static function table(Table $table): Table
    {
        return $table
            ->query(Ordene::query())
            ->columns([
                TextColumn::make('acta')
                    ->label('ACTA'),
                TextColumn::make('actividad.tipo_actividad')
                    ->label('ACTIVIDAD'),
                TextColumn::make('fecha')
                    ->label('FECHA')
                    ->sortable()
                    ->searchable()
                    ->dateTime('d/m/Y '),
                TextColumn::make('ticket')
                    ->label('TICKET')
                    ->searchable(),
                TextColumn::make('abonado.nombre')
                    ->label('ABONADO')
                    ->searchable(),
                TextColumn::make('abonado.plan')
                    ->label('PLAN')
                    ->searchable(),
            ])->filters([

            ])->actions([
                DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotificationTitle('Registro eliminado correctamente'),
                EditAction::make()->label('Editar')
                    ->url(fn (Ordene $record) => route('filament.admin.pages.admin.ingreso.{ordenId?}', ['ordenId' => $record->id]))

            ])->bulkActions([

            ]);
    }
}
