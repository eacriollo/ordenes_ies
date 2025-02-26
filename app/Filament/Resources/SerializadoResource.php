<?php

namespace App\Filament\Resources;

use App\Exports\EquiposExporter;
use App\Filament\Resources\SerializadoResource\Pages;
use App\Filament\Resources\SerializadoResource\RelationManagers;
use App\Imports\equiposNuevosImport;
use App\Models\Material;
use App\Models\Serializado;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SerializadoResource extends Resource
{
    protected static ?string $model = Serializado::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('serie')
                    ->required()
                    ->unique('serializados', 'serie')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha')
                    ->required(),
                Forms\Components\Select::make('estado')
                    ->options([
                        'Disponible' => 'Disponible',
                        'Vendido' => 'Vendido',
                        'Instalado' => 'Instalado',
                        'Dañado' => 'Dañado',
                        'Devuelto' => 'Devuelto',
                        'Retirado' => 'Retirado',
                    ])
                    ->required(),
                Forms\Components\Select::make('material_id')
                    ->relationship('material', 'nombre')
                    ->options(Material::where('tipo', 'serializado')->pluck('nombre', 'id')->toArray())
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make('exportar')
                    ->label('Reporte')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->required(),
                        Forms\Components\DatePicker::make('fecha_fin')
                            ->label('Fecha de fin')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        return Excel::download(new EquiposExporter($data['fecha_inicio'], $data['fecha_fin']),
                            'Equipos_' . $data['fecha_inicio'] . '_a_' . $data['fecha_fin'] . '.xlsx'
                        );
                    })
                    ->after(function () {
                        Notification::make()
                            ->title('Exportación completada')
                            ->body('El archivo se ha descargado correctamente.')
                            ->success()
                            ->send();
                    }),


            ])
            ->columns([
                Tables\Columns\TextColumn::make('serie')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado'),
                Tables\Columns\TextColumn::make('material.nombre')
                    ->label('Nombre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSerializados::route('/'),
            'create' => Pages\CreateSerializado::route('/create'),
            'edit' => Pages\EditSerializado::route('/{record}/edit'),
        ];
    }
}
