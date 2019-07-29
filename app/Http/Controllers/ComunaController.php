<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PuntoEvaluacion;
use App\Comuna;
use DB;
use League\Fractal;
use App\Transformers\ComunaTransformer;
use League\Fractal\Manager;

class ComunaController extends ApiController {

    /**
     * Retorna todas las comunas
     *
     * GET
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        try{
            $comunas = Comuna::has('punto_evaluacion', '>=', config('constants.evaluaciones_minimas'))
                ->exclude(['geom', 'shape_leng', 'shape_area', 'shape_le_1', 'dis_elec', 'cir_sena', 'cod_comuna'])
                ->get()
                ->sortBy('com_orden');

            $resource = new Fractal\Resource\Collection($comunas, new ComunaTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();

            return $this->respond($data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }

    /**
     * Busca la región y la comuna más o menos accesible, dependiendo del "valor" entregado. El porcentaje
     * entregado corresponde al porcentaje de "valor" por comuna. por ejemplo 20% de puntos accesibles
     *
     * @param $evaluacion_ponderada valor de la evaluación ponderada del punto
     * @param $cantidad
     * @return mixed
     *
    public function consultaRegionesComunas($evaluacion_ponderada, $cantidad)
    {
        $query = "SELECT 
                    reg.reg_nombre as region,  
                    com.com_nombre as comuna, 
                    count(pev_evaluacion_ponderada) as evaluaciones, 
                    (count(pev_evaluacion_ponderada) * 100 / (SELECT count(*) 
                      FROM 
                        sacf_bd.punto_evaluacion pev2
                      WHERE 
                        pev2.pev_com_id = pev1.pev_com_id
                    )) as porcentaje
                  FROM
                    sacf_bd.punto_evaluacion pev1, 
                    sacf_bd.comuna com, 
                    sacf_bd.provincia pro, 
                    sacf_bd.region reg
                  WHERE
                    pev_tpe_id > 1 AND
                    pev_evaluacion_ponderada = ".$evaluacion_ponderada." AND
                    pev_com_id = com.com_id AND
                    com.com_pro_id = pro.pro_id AND
                    pro.pro_reg_id = reg.reg_id
                GROUP BY reg_nombre, pev_com_id, com.com_nombre
                ORDER BY porcentaje DESC limit ".$cantidad;

        $resultado = DB::select(DB::raw($query));

        return $resultado;
    }*/

    public function region_comuna()
    {
        try{
            $comunas = PuntoEvaluacion::porComuna();
            $regiones = PuntoEvaluacion::porRegion();

            return response()->json([
                'comuna' => [
                    'mas_accesible' => $comunas->first()->com_nombre,
                    'menos_accesible' => $comunas->last()->com_nombre,
                ],
                'region' => [
                    'mas_accesible' => $regiones->first()->reg_nombre,
                    'menos_accesible' => $regiones->last()->reg_nombre,
                ]
            ]);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }

    public function region_comuna_mas_evals()
    {
        try{
            $mas_evals_edificaciones = $this->region_comuna_mas_evals_pto(2);
            $mas_evals_esp = $this->region_comuna_mas_evals_pto(3);
            $mas_evals_vereda = $this->region_comuna_mas_evals_pto(4);

            return response()->json([
                'edificaciones' => $mas_evals_edificaciones[0],
                'espacios' => $mas_evals_esp[0],
                'veredas_cruces' => $mas_evals_vereda[0]
            ]);

        }catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }

    /**
     * Obtiene la región y la comuna con más evaluaciones por tipo de punto de evaluación
     *
     * @param $tipo_punto_evaluacion
     * @return \Illuminate\Http\JsonResponse
     */
    public function region_comuna_mas_evals_pto($tipo_punto_evaluacion)
    {
        try{
            $pev_query = 'SELECT 
                        pev_id, 
                        pev_titulo, 
                        pev_com_id,
                        pev_evaluacion_ponderada
                    FROM 
                        sacf_bd.punto_evaluacion AS pe
                    WHERE 
                      pev_tpe_id ';

            if($tipo_punto_evaluacion <= 3){
                $pev_query .= "= ".$tipo_punto_evaluacion;
            } else {
                $pev_query .= "> 3";
            }

            $sec_select = 'SELECT 
                            com_nombre,  
                            pr.pro_id, 
                            reg_nombre, 
                            count(pev_evaluacion_ponderada) AS cantidad
                        FROM
                            ('.$pev_query.') AS suma, 
                            sacf_bd.comuna AS com, 
                            sacf_bd.provincia AS pr, 
                            sacf_bd.region AS reg
                        WHERE 
                            pev_com_id = com.com_id AND
                            com.com_pro_id = pr.pro_id AND
                            pr.pro_reg_id = reg.reg_id
                        GROUP BY com.com_nombre,pr.pro_id, reg.reg_nombre';

            $query = 'SELECT 
                        com_nombre AS comuna, 
                        reg_nombre AS region, 
                        cantidad 
                    FROM
                      ('.$sec_select.') AS total
                    WHERE 
                        cantidad = (SELECT max(cantidad) FROM ('.$sec_select.') AS total2
                    ) 
                    LIMIT 1';

           $result = DB::select(DB::raw($query));

           return $result;

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }

    /**
     * Obtiene las comunas de acuerdo al porcentaje de accesibilidad calculando
     * la sumatoria de sus ponderadas vs. la cantidad de evaluaciones
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tasaAccesibilidad()
    {
        try{
            $comunas_tasa = PuntoEvaluacion::porComuna(false)->take(10);

            return response()->json($comunas_tasa);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }
}
