<?php

namespace App\Imports;

use App\Models\Serializado;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class equiposDevueltosImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public static $equiposNoEncontrados = [];

    public function model(array $row)
    {

        // Buscar el equipo por serie
        $equipo = Serializado::where('serie', $row['serie'])->first();

        if ($equipo) {
            // Si el equipo existe, actualiza el estado
            $equipo->update([
                'estado' => $row['estado'],
                'fecha' => $this->transformDate($row['fecha']),// Actualiza el estado con el valor del archivo
            ]);
        } else {
            // Si el equipo no existe, puedes manejarlo como desees (puedes retornarlo null para ignorarlo o loguearlo)
            self::$equiposNoEncontrados[] = $row['serie'];
        }

        // Si el equipo no existe, puedes manejarlo como desees (puedes retornarlo null para ignorarlo o loguearlo)
        return null;
    }

    public function transformDate($value)
    {
        if (is_numeric($value)){
            return Carbon::createFromDate(1900, 1, 1)->addDay($value - 2) ->format('Y-m-d');
        }
        return Carbon::parse($value)->format('Y-m-d');
    }

    public static function getEquiposNoEncontrados()
    {
        return self::$equiposNoEncontrados;
    }
}
