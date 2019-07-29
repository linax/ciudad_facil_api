<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValorItemPuntoEvaluacion extends Model
{
    use SoftDeletes;

    protected $table = 'valor_item_punto_evaluacion';

    protected $primaryKey = 'vie_id';

    protected $hidden = [
        'pivot'
    ];

    protected $dates = [
        'deleted_at'
    ];

    /*
     * relaciones
     */
    public function item_evaluar()
    {
        return $this->belongsTo(ItemEvaluar::class, 'vie_iev_id');
    }

    public function evaluacion_usuario_punto_evaluacion()
    {
        return $this->belongsToMany(EvaluacionUsuarioPuntoEvaluacion::class, 'valor_evaluacion', 'vae_vie_id',  'vae_eup_id')->withTimestamps();
    }

    /*
     * métodos del modelo
     */
    public static function identificadores_validos()
    {
        return implode(',', ValorItemPuntoEvaluacion::all(['vie_id'])->pluck('vie_id')->toArray());
    }
}
