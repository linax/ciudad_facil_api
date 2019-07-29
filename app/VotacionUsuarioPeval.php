<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VotacionUsuarioPeval extends Model
{
    use SoftDeletes;

    protected $table = 'votacion_usuario_peval';

    protected $primaryKey = 'vot_id';

    protected $fillable = [
        'vot_intencion_votacion',
    ];

    public static $field_mask = [
        'vot_tpe' => 'vot_intencion_votacion'
    ];

    protected $dates = [
        'delete_at'
    ];


    /*
     * relaciones
     *
    public function punto_evaluacion()
    {
        return $this->belongsTo(PuntoEvaluacion::class, 'opu_pev_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'opu_usu_id');
    }

    public function opinion_comentario()
    {
        return $this->hasMany(ComentarioUsuarioPeval::class, 'opc_opu_id');
    }*/
}
