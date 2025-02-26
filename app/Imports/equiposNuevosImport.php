<?php

namespace App\Imports;

use App\Models\Serializado;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;

class equiposNuevosImport implements ToModel, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    use Importable;

    public function model(array $row)
    {
        if (Serializado::where('serie', $row[0])->exists()) {
            return null;
        }
        return new Serializado([
            //
            'serie' => $row[0],
            'fecha' => $this->transformDate($row[1]),
            'estado' => $row[2],
            'material_id' => $row[3],
        ]);
    }

    public function transformDate($value)
    {
        if (is_numeric($value)){
            return Carbon::createFromDate(1900, 1, 1)->addDay($value - 2) ->format('Y-m-d');
        }
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function rules(): array
    {
        return [
            '0' => 'required',
            '1' => 'required',
            '2' => 'required',
            '3' => 'required',
        ];
    }


}
