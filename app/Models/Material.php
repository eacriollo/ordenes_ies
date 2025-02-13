<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    //

    protected $fillable = [
        'nombre',
        'codigo',
        'tipo',
        'stock',
        'unidad'
    ];

    public function orden(): belongsToMany
    {
        return $this->belongsToMany(Ordene::class , 'material_ordenes')
            ->withPivot('cantidad','serializado_id')
            ->withTimestamps();
    }

    public function serializados()
    {
        return $this->hasMany(Serializado::class, 'material_id');
    }

}
