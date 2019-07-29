<?php

namespace App;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaEdificacion extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'categoria_edificacion';

    protected $primaryKey = 'cat_id';

    protected $fillable = [
    	'cat_nombre',
    	'cat_orden'
    ];

    protected $dates = [
    	'deleted_at'
    ];

    protected $cascadeDeletes = [
        'subcategoria_edificacion'
    ];

    public function categoria_edificacion()
    {
    	return $this->hasMany(SubcategoriaEdificacion::class, 'sub_cat_id');
    }
}