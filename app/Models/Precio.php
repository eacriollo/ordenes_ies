<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Precio extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'precio',
        'activo',
    ];

    public function Ordenes(): HasMany
    {
        return $this->hasMany(Ordene::class);
    }
}
