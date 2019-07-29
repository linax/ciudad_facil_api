<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VeredaCruce extends Model
{
    use SoftDeletes;

    protected $table = 'vereda_cruce';

    protected $primaryKey = 'vcr_id';

    protected $fillable = [
        'vcr_tipo',
        'vcr_comentario_senadis',
        'vcr_descripcion',
        'vcr_mejora_senadis',
    ];

    public static $field_mask = [
        'vcr_tip' => 'vcr_tipo',
        'vcr_des' => 'vcr_descripcion',
        'esp_tel' => 'esp_telefono',
        'esp_cat' => 'esp_telefono',
        'vcr_mjr_snd' => 'vcr_mejora_senadis',
        'vcr_cmn_snd' => 'vcr_comentario_senadis'
    ];

    protected $dates = [
        'deleted_at'
    ];

    /**
     * accessors
     */
    public function getVcrMejoraSenadisAttribute($value)
    {
        return boolval($value);
    }

    /**
     * relaciones del modelo
     */
    public function punto_evaluacion()
    {
        return $this->belongsTo(PuntoEvaluacion::class, 'vcr_pev_id');
    }

    /*public function ponderadasVereda()
    {
        $valor_item_punto_evaluaciones = $this->punto_evaluacion->evaluacion_usuario_punto_evaluacion->map(function($evaluacion_usuario_punto_evaluacion){
            return $evaluacion_usuario_punto_evaluacion->valor_item_punto_evaluacion;
        })->flatten();

        return ItemEvaluar::where('iev_tpe_id', 4)->get()->map(function($item_evaluar) use ($valor_item_punto_evaluaciones){print_r($valor_item_punto_evaluaciones->where('vie_iev_id', $item_evaluar->iev_id)->count());
            return [$item_evaluar->iev_nombre_dato => (!empty($denominador = $valor_item_punto_evaluaciones->where('vie_iev_id', $item_evaluar->iev_id)->count())) ? $valor_item_punto_evaluaciones->where('vie_iev_id', $item_evaluar->iev_id)->sum('vie_valor')/$denominador : null];
        });
    }*/
}
