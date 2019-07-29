<?php

namespace App;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaEspacioPublico extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'categoria_espacio_publico';

    protected $primaryKey = 'cep_id';

    protected $fillable = [
        'cep_nombre',
        'cep_orden'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $cascadeDeletes = [
        'subcategoria_espacio_publico'
    ];

    /*
     * relaciones
     */
    public function subcategoria_espacio_publico()
    {
        return $this->hasMany(SubcategoriaEspacioPublico::class, 'ces_cep_id');
    }

    /*
     * funciones
     */
    public static function identificadores_validos()
    {
        return implode(',', CategoriaEspacioPublico::all(['ces_id'])->pluck('ces_id')->toArray());
    }
}
