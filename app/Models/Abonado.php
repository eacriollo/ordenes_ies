<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class Abonado extends Model
{
    //

    use HasRoles;

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
