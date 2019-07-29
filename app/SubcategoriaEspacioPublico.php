<?php

namespace App;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubcategoriaEspacioPublico extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'subcategoria_espacio_publico';

    protected $primaryKey = 'ces_id';

    protected $fillable = [
        'ces_nombre',
        'ces_orden'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $cascadeDeletes = [
        'espacio_publico'
    ];

    /*
     * relaciones
     */
    public function categoria_espacio_publico()
    {
        return $this->belongsTo(CategoriaEspacioPublico::class, 'ces_cep_id');
    }

    public function espacio_publico()
    {
        return $this->hasMany(EspacioPublico::class, 'esp_ces_id');
    }

    /*
     * funciones
     */
    public static function identificadores_validos()
    {
        return implode(',', SubcategoriaEspacioPublico::all(['ces_id'])->pluck('ces_id')->toArray());
    }
}