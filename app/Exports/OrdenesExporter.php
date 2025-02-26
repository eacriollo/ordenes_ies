<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Ordene;

class OrdenesExporter implements FromCollection, WithHeadings, WithMapping
{
    protected $fechaInicio;
    protected $fechaFin;

    // Constructor para recibir fechas
    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    // Filtramos los datos a exportar
    public function collection()
    {
        return Ordene::whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
            ->with('abonado', 'actividad', 'precio')
            ->get();
    }

    public function headings(): array
    {
        return [
            'actividad',
            'precio',
            'fecha',
            'codigo',
            'nombre',
            'plan',
            'ticket',
            'acta'
        ];
    }

    public function map($orden): array
    {
        //dd($orden);
        return [
            $orden->actividad->tipo_actividad,
            $orden->precio->precio,
            $orden->fecha,
            $orden->abonado->codigo,
            $orden->abonado->nombre,
            $orden->abonado->plan,
            $orden->ticket,
            $orden->acta,

        ];
    }

}
