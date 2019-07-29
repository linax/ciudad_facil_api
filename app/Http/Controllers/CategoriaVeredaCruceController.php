<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoriaVeredaCruceController extends ApiController
{
    /**
     * Obtiene las categorías de vereda cruce
     *
     * GET
     *
     * @param  Request $request
     * @return json 			Datos de las categorías
     */
    public function show()
    {
        try{
            $data = [
                'data' => [
                    [
                        'vcr_id' => 4,
                        'vcr_nam' => 'Vereda'
                    ],
                    [
                        'vcr_id' => 5,
                        'vcr_nam' => 'Cruce',
                    ]
                ]
            ];

            return $this->respond('Categorías de Vereda Cruce', $data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }
}
