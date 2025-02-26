<?php

namespace App\Filament\Widgets;


use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class TecnicosChart extends ChartWidget
{

    protected static ?string $heading = 'Chart';

    public $chartData = [];

   // protected $listeners = ['tecnicosChart'];

    #[On('actualizarDatosTecnicosChart')]

    public function actualizarDatos($datos)
    {
        $this->chartData = $datos;
        //dd($this->chartData);
    }


    protected function getData(): array
    {

        return [

            'datasets' => [
                [
                    'label' => 'Ordenes',
                    'data' => array_values($this->chartData),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                ],
            ],
            'labels' => array_keys($this->chartData),


        ];


    }

    protected function getType(): string
    {
        return 'bar';
    }

    /*public static function canView(): bool
    {
        return true;
    }*/
}
