<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrecioResource\Pages;
use App\Filament\Resources\PrecioResource\RelationManagers;
use App\Models\Precio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrecioResource extends Resource
{
    protected static ?string $model = Precio::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

        public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('precio')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'El precio ya se encuentra registrado',
                    ])
                    ->numeric(),
                Forms\Components\Toggle::make('activo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('precio')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
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
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
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
            'index' => Pages\ListPrecios::route('/'),
            'create' => Pages\CreatePrecio::route('/create'),
            'edit' => Pages\EditPrecio::route('/{record}/edit'),
        ];
    }
}
