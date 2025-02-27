<?php

namespace App\Exports;


use App\Models\serializado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class EquiposExporter implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        //
        return Serializado::whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
            ->with('material', 'materialOrdene.ordenes.abonado', 'materialOrdene.ordenes.persona' )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Equipo',
            'Serie',
            'Plan',
            'Codigo',
            'Persona',
            'Estado',

        ];
    }

    public function map($material): array
    {
        //dd($material);
        return [
            $material->material->nombre,
            $material->serie,
           $material->materialOrdene->ordenes->abonado->plan,
            $material->materialOrdene->ordenes->abonado->codigo,
            $material->materialOrdene->ordenes->persona->nombre,
            $material->estado,
        ];
    }
}
