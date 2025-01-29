<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class material_ordene extends Model
{
    //
    protected $fillable = [
        'material_id',
        'ordene_id',
        'cantidad',
    ];
}
