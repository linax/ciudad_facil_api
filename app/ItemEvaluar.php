<?php

namespace App;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemEvaluar extends Model
{
    use SoftDeletes,CascadeSoftDeletes;

    protected $table = 'item_evaluar';

    protected $primaryKey = 'iev_id';

    protected $dates = [
        'deleted_at'
    ];

    protected $cascadeDeletes = [
        'valor_item_punto_evaluacion'
    ];

    /*
     * relacioens
     */
    public function valor_item_punto_evaluacion()
    {
        return $this->hasMany(ValorItemPuntoEvaluacion::class, 'vie_iev_id');
    }

    public function tipo_punto_evaluacion()
    {
        return $this->belongsTo(TipoPuntoEvaluacion::class, 'iev_tpe_id');
    }

}
