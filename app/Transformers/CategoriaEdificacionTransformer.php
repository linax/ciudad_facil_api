<?php

namespace App\Transformers;

use App\CategoriaEdificacion;
use League\Fractal\TransformerAbstract;

class CategoriaEdificacionTransformer extends TransformerAbstract
{
    public function transform(CategoriaEdificacion $categoria_edificacion)
    {
        $subcategorias_edificacion = $categoria_edificacion->categoria_edificacion;

        return [
            'cat_id' => $categoria_edificacion->cat_id,
            'cat_nam' => $categoria_edificacion->cat_nombre,
            'cat_sub' => $subcategorias_edificacion->map(function($subcategoria){
                return [
                    'sub_id' => $subcategoria->sub_id,
                    'sub_nam' => $subcategoria->sub_nombre
                ];
            })->sortBy('sub_orden')->values()->all()
        ];
    }
}