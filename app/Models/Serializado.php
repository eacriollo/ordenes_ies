<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class serializado extends Model
{
    //
    protected $fillable = [
        'serie',
        'fecha',
        'estado',
        'material_id'
    ];

   public function material(): BelongsTo
   {
       return $this->belongsTo(Material::class);
   }

    public function materialOrdene()
    {
        return $this->hasOne(MaterialOrdene::class);
    }

}
