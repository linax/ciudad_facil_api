<?php

namespace App\Transformers;

use App\ItemEvaluar;
use League\Fractal\TransformerAbstract;

class ItemEvaluarTransformer extends TransformerAbstract {

    public function transform(ItemEvaluar $item_evaluar)
    {
        return [
            'iev_des' => $item_evaluar->iev_descripcion,
            'vie' => $item_evaluar->valor_item_punto_evaluacion->sortBy('vie_id')->map(function($valor_item_punto_evaluacion){
                return [
                    'vie_id' => $valor_item_punto_evaluacion->vie_id,
                    'vie_dsc' => $valor_item_punto_evaluacion->vie_descripcion
                ];
            })->sortBy('vie_orden')->values()->all(),
        ];
    }

}