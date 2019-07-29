<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edificacion extends Model
{
    use SoftDeletes;

    protected $table = 'edificacion';

    protected $primaryKey = 'edi_id';

    protected $fillable = [
        'edi_sub_id',
        'edi_pev_id',
        'edi_direccion',
        'edi_email',
        'edi_telefono',
        'edi_publico'
    ];

    public static $field_mask = [
        'edi_dir' => 'edi_direccion',
        'edi_ema' => 'edi_email',
        'edi_pub' => 'edi_publico',
        'edi_tel' => 'edi_telefono',
        'edi_sub_cat' => 'edi_sub_id',
    ];

    protected $dates = [
    	'deleted_at'
    ];

    public function punto_evaluacion()
    {
        return $this->belongsTo(PuntoEvaluacion::class, 'edi_pev_id');
    }

    public function subcategoria_edificacion()
    {
    	return $this->belongsTo(SubcategoriaEdificacion::class, 'edi_sub_id');
    }
}
