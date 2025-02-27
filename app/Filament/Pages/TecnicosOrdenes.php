<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Widgets\TecnicosChart;
use App\Models\Ordene;
use App\Models\Persona;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Forms;


class TecnicosOrdenes extends Page
{

    use InteractsWithForms, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.tecnicos-ordenes';

    protected static ?string $navigationGroup = 'Reportes';

    public $mesSeleccionado;
    public $tecnicoSeleccionado;


    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Tecnicos')
                ->schema([
                    DatePicker::make('mesSeleccionado')
                        ->format('Y-m')
                        ->required()
                        ->label('Seleccionar Mes'),


                    Select::make('tecnicoSeleccionado')
                        ->label('Seleccionar Técnico')
                        ->options(fn() => Persona::pluck('nombre', 'id'))
                        ->required()
                        ->reactive(),
                    //->afterStateUpdated(),
                ])->columns(2)
                ->model(Persona::class)

        ];
    }

    protected function getHeaderActions(): array
    {


        return [
            Action::make('TECNICOS')
                ->label('TECNICOS')
                ->action(function () {
                    $this->reporte();
                }),
        ];

    }


    public function reporte()
    {
        $data = $this->form->getState();

        $idTecnico = $data['tecnicoSeleccionado'];
        $mes = $data['mesSeleccionado'];

        //dd($mes);

        $datos = Ordene::where('persona_id', $idTecnico)
            ->whereMonth('fecha', date('m', strtotime($mes)))
            ->whereYear('fecha', date('Y', strtotime($mes)))
            ->with('actividad')
            ->groupBy('actividad_id')
            ->selectRaw('actividad_id, COUNT(*) as cantidad')
            ->get()
            ->pluck('cantidad', 'actividad.tipo_actividad')
            ->toArray();


        $this->dispatch('actualizarDatosTecnicosChart', datos: $datos);


    }


    protected function getFooterWidgets(): array
    {
        return [
            TecnicosChart::class,
        ];
    }




}
