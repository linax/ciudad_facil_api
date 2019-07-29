<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoPuntoEvaluacion;
use League\Fractal\Resource\Collection;
use App\Transformers\ItemEvaluarTransformer;
use League\Fractal\Manager;
use App\ItemEvaluar;

class TipoPuntoEvaluacionController extends ApiController
{
    /**
     * Retorna todos las categorias de evaluacion
     *
     * GET
     *
     * @param  TipoPuntoEvaluacion
     * @return json 			Lista de todos los puntos de evaluacion
     */
    public function show(TipoPuntoEvaluacion $tipo_punto_evaluacion)
    {
        switch ($tipo_punto_evaluacion->tpe_id) {
            case 2: $filtro = 'edi';break;
            case 3: $filtro = 'esp';break;
            case 4: $filtro = 'ver';break;
            case 5: $filtro = 'cru';break;
        }

        $items_evaluar = ItemEvaluar::where('iev_nombre_dato', 'like', $filtro.'_%')->orderBy('iev_id')->get();

        $resource = new Collection($items_evaluar, new ItemEvaluarTransformer());
        $fractal = new Manager();
        $data = $fractal->createData($resource)->toArray();

        return $this->respond('Categorías de evaluación', $data);
    }
}
