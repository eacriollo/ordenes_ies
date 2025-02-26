<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class materialOrdene extends Model
{
    //

    protected $table = 'material_ordenes';
    protected $fillable = [
        'material_id',
        'ordene_id',
        'cantidad',
    ];

    public function serializado()
    {
        return $this->belongsTo(serializado::class);
    }

    public function orden()
    {
        return $this->belongsTo(Ordene::class, 'ordene_id');
    }

}
