<?php

namespace App;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoPuntoEvaluacion extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'tipo_punto_evaluacion';

    protected $primaryKey = 'tpe_id';

    protected $dates = [
        'deleted_at'
    ];

    protected $cascadeDeletes = [
        'item_evaluar'
    ];

    /*
     * relaciones
     */
    public function item_evaluar()
    {
        return $this->hasMany(ItemEvaluar::class, 'iev_tpe_id');
    }

    public function punto_evaluacion()
    {
        return $this->hasMany(PuntoEvaluacion::class, 'pev_tpe_id');
    }
}
