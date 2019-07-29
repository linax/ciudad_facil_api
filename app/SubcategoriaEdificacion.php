<?php

namespace App;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubcategoriaEdificacion extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'subcategoria_edificacion';

    protected $primaryKey = 'sub_id';

    protected $fillable = [
    	'sub_nombre',
    	'sub_orden'
    ];

    public static $field_mask = [
    	'sub_id' => 'sub_id',
    ];

    protected $dates = [
    	'deleted_at'
    ];

    protected $cascadeDeletes = [
        'edificacion'
    ];

    /*
     * relaciones
     */
    public function categoria_edificacion()
    {
    	return $this->belongsTo(CategoriaEdificacion::class, 'sub_cat_id');
    }

    public function edificacion()
    {
    	return $this->hasOne(Edificacion::class, 'edi_sub_id');
    }

    /*
     * funciones
     */
    public static function identificadores_validos()
    {
        return implode(',', SubcategoriaEdificacion::all(['sub_id'])->pluck('sub_id')->toArray());
    }
}
