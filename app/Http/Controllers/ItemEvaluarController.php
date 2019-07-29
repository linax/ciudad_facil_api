<?php

namespace App\Http\Controllers;

use App\ItemEvaluar;
use League\Fractal;
use App\Transformers\ItemEvaluarTransformer;
use League\Fractal\Manager;
use App\PuntoEvaluacion;

class ItemEvaluarController extends ApiController
{
    public function all(PuntoEvaluacion $punto_evaluacion)
    {
        try{
            switch ($punto_evaluacion->pev_tpe_id) {
                case 2: $filtro = 'edi';break;
                case 3: $filtro = 'esp';break;
                case 4:
                case 5:
                    switch ($punto_evaluacion->vereda_cruce->vcr_tipo) {
                        case 1: $filtro = 'ver';break;
                        case 2: $filtro = 'cru';break;
                    }
            }

            $items_evaluar = ItemEvaluar::where('iev_nombre_dato', 'like', $filtro.'_%')->orderBy('iev_id')->get();

            $resource = new Fractal\Resource\Collection($items_evaluar, new ItemEvaluarTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();

            return $this->respond($data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }

    public function itemsEval($pev_tpe_id)
    {
        try{
            $items = ItemEvaluar::where('iev_tpe_id', $pev_tpe_id)->orderBy('iev_id')->get();

            $resource = new Fractal\Resource\Collection($items, new ItemEvaluarTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();
            return $this->respond($data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }
}
