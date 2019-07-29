<?php

namespace App\Transformers;

use App\CategoriaEspacioPublico;
use League\Fractal\TransformerAbstract;

class CategoriaEspacioPublicoTransformer extends TransformerAbstract
{
    public function transform(CategoriaEspacioPublico $categoria_espacio_publico)
    {
        $subcategorias_espacio_publico = $categoria_espacio_publico->subcategoria_espacio_publico;

        return [
            'cep_id' => $categoria_espacio_publico->cep_id,
            'cep_nam' => $categoria_espacio_publico->cep_nombre,
            'cep_ces' => $subcategorias_espacio_publico->map(function($subcategoria_espacio_publico){
                return [
                    'ces_id' => $subcategoria_espacio_publico->ces_id,
                    'ces_nam' => $subcategoria_espacio_publico->ces_nombre
                ];
            })->sortBy('ces_orden')->values()->all()
        ];
    }
}