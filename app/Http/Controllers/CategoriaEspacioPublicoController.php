<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CategoriaEspacioPublico;
use League\Fractal;
use App\Transformers\CategoriaEspacioPublicoTransformer;
use League\Fractal\Manager;

class CategoriaEspacioPublicoController extends ApiController
{
    /**
     * Obtiene las categorías de espacio público
     *
     * GET
     *
     * @param  Request $request
     * @return json 			Datos de las categorías
     */
    public function show()
    {
        try{
            $categorias = CategoriaEspacioPublico::all()->sortBy('cep_orden');

            $resource = new Fractal\Resource\Collection($categorias, new CategoriaEspacioPublicoTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();

            return $this->respond('Categorías de espacio público', $data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }
}

