<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EspacioPublico extends Model
{
    use SoftDeletes;

    protected $table = 'espacio_publico';

    protected $primaryKey = 'esp_id';

    protected $fillable = [
        'esp_direccion',
        'esp_email',
        'esp_telefono',
    ];

    public static $field_mask = [
        'esp_dir' => 'esp_direccion',
        'esp_ema' => 'esp_email',
        'esp_tel' => 'esp_telefono',
        'esp_cat' => 'esp_telefono',
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function punto_evaluacion()
    {
        return $this->belongsTo(PuntoEvaluacion::class, 'esp_pev_id');
    }

    public function subcategoria_espacio_publico()
    {
        return $this->belongsTo(SubcategoriaEspacioPublico::class, 'esp_ces_id');
    }
}
