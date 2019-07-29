<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Region;
use App\PuntoEvaluacion;
use League\Fractal;
use App\Transformers\RegionTransformer;
use League\Fractal\Manager;

class RegionController extends ApiController{

    /**
     * Retorna todas las regiones
     *
     * GET
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        try{
            $regiones = Region::all()->sortBy('reg_orden');

            $resource = new Fractal\Resource\Collection($regiones, new RegionTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();

            return $this->respond($data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }

    /*public function tres_regiones()
    {
        try{
            $tres_regiones = app('App\Http\Controllers\ComunaController')->consultaRegionesComunas(2, 3);

            return response()->json($tres_regiones);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }
    /**
     * Obtiene el conteo de evaluaciones por region
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function conteoEvaluaciones()
    {
        try{
            $evaluaciones_region = $evaluaciones_region_resultado = [];

            PuntoEvaluacion::porComuna(false)->each(function($comuna) use (&$evaluaciones_region){
                $reg_nom = $comuna->provincia->region->reg_nombre;
                if (!isset($evaluaciones_region[$reg_nom])){
                    $evaluaciones_region[$reg_nom] = 0;
                }
                $evaluaciones_region[$reg_nom] += $comuna->eval_count;
            });

            $suma_evaluaciones_region = array_sum($evaluaciones_region);

            foreach ($evaluaciones_region as $key => $item){
                array_push($evaluaciones_region_resultado,
                    [
                        'label' => $key,
                        'data' => round($item * 100 / $suma_evaluaciones_region,2)
                    ]);
            }

            return response()->json($evaluaciones_region_resultado);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }
}
