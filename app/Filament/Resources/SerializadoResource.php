<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SerializadoResource\Pages;
use App\Filament\Resources\SerializadoResource\RelationManagers;
use App\Models\Material;
use App\Models\Serializado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
