<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'tipo_actividad'
    ];

    public function ordenes(): HasMany
    {
        return $this->hasMany(Ordene::class);
    }
}
