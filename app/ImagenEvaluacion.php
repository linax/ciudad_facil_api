<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImagenEvaluacion extends Model
{
    use SoftDeletes;

    protected $table = 'imagen_evaluacion';

    protected $primaryKey = 'ima_id';

    protected $fillable = [
        'ima_imagen'
    ];

    public static $field_mask = [
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function evaluacion_usuario_punto_evaluacion()
    {
        return $this->belongsTo(EvaluacionUsuarioPuntoEvaluacion::class, 'ima_eup_id');
    }
}
