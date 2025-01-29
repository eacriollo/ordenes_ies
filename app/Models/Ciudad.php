<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ciudad extends Model
{
    //
    use HasFactory;

    protected $fillable = ['nombre', 'estado'];

    public function Ordenes(): HasMany
    {
        return $this->hasMany(Ordene::class);
    }
}
