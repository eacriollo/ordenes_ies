<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Abonado extends Model
{
    //

    protected $fillable = [
        'nombre',
        'plan',
        'codigo',
    ];

    public function ordenes(): HasMany
    {
        return $this->hasMany(Ordene::class);
    }
}
