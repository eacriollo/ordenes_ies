<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ordene extends Model
{
    //
    protected $fillable = [
      'fecha',
      'acta',
      'ticket',
      'manga',
      'observaciones',
      'precio_id',
      'persona_id',
      'actividad_id',
      'abonado_id',
      'user_id',
      'ciudad_id',
    ];

    public function abonado(): belongsTo
    {
        return $this->belongsTo(Abonado::class);
    }

    public function precio(): belongsTo
    {
        return $this->belongsTo(Precio::class);
    }

    public function actividad(): belongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function ciudad(): belongsTo
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function persona(): belongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function materiales(): belongsToMany
    {
        return $this->belongsToMany(Material::class , 'material_ordenes')
            ->withPivot('cantidad','serializado_id')
            ->withTimestamps();
    }
}
