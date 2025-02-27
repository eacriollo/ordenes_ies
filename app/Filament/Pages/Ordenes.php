<?php

namespace App\Filament\Pages;


use App\Exports\OrdenesExporter;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Pages\Page;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use App\Models\Ordene;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\DatePicker;


class Ordenes extends Page implements HasTable
{
    use InteractsWithTable, HasPageShield;


    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string $view = 'filament.pages.ordenes';

    protected static ?string $navigationGroup = 'Resgistro';

    public static function table(Table $table): Table
    {
        return $table
            ->query(Ordene::query())
            ->headerActions([
                ExportAction::make('exportar')
                    ->label('Exportar órdenes')
                    ->color('success')
                    ->form([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->required(),
                        DatePicker::make('fecha_fin')
                            ->label('Fecha de fin')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        return Excel::download(new OrdenesExporter($data['fecha_inicio'], $data['fecha_fin']),
                            'ordenes_' . $data['fecha_inicio'] . '_a_' . $data['fecha_fin'] . '.xlsx'
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
                    ->url(fn(Ordene $record) => route('filament.admin.pages.admin.ingreso.{ordenId?}', ['ordenId' => $record->id]))

            ])->bulkActions([
                // ExportBulkAction::make()
            ]);
    }

}
