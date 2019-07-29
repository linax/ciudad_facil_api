<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CategoriaEdificacion;
use League\Fractal;
use App\Transformers\CategoriaEdificacionTransformer;
use League\Fractal\Manager;

class CategoriaEdificacionController extends ApiController
{
    /**
     * Obtiene las categorías de edificación y sus sub categorías
     *
     * GET
     *
     * @param  Request $request
     * @return json 			Datos de las categorías
     */
    public function show()
    {
        try{
            $categorias = CategoriaEdificacion::all()->sortBy('cat_orden');

            $resource = new Fractal\Resource\Collection($categorias, new CategoriaEdificacionTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();

            return $this->respond('Categorías de edificación', $data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }
}
