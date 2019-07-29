<?php

namespace App\Http\Controllers;

use App\ComentarioUsuarioPeval;
use App\EvaluacionUsuarioPuntoEvaluacion;
use App\PuntoEvaluacion;
use App\Transformers\PuntoEvaluacionTransformer;
use App\ValorItemPuntoEvaluacion;
use App\VotacionUsuarioPeval;
use App\ItemEvaluar;
use App\Region;
use App\Comuna;
use Carbon\Carbon;
use Illuminate\Http\Request;
use JWTAuth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Validator;

class PuntoEvaluacionController extends ApiController
{
    /**
     * Retarna todos los puntos de evaluacion del mapa
     *
     * GET
     *
     * @return json     Lista de todos los puntos de evaluacion
     */
    public function show()
    {
        $puntos_evaluacion = PuntoEvaluacion::all();

        $resource = new Collection($puntos_evaluacion, new PuntoEvaluacionTransformer());
        $fractal = new Manager();
        $data = $fractal->createData($resource)->toArray();

        return $this->respond('Puntos de evaluación', $data);
    }

    /**
     * Ingresa una nueva evaluación a un punto de evaluación
     *
     * POST
     *
     * @param  Request $request Información de la evlauación
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function evaluate(Request $request, PuntoEvaluacion $punto_evaluacion)
    {
        $validator = Validator::make($request->all(), [
            'vae.*' => 'required|in:'.ValorItemPuntoEvaluacion::identificadores_validos(),
            'eup_cmn' => 'string|max:255|nullable',
            'ima_img.*' => 'required|base64image'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 422);
        }

        try{
            $usuario = JWTAuth::toUser();

            $evaluacion_usuario_punto_evaluacion = new EvaluacionUsuarioPuntoEvaluacion([
                EvaluacionUsuarioPuntoEvaluacion::$field_mask['eup_cmn'] => $request->input('eup_cmn')
            ]);

            $evaluacion_usuario_punto_evaluacion->usuario()->associate($usuario);
            $evaluacion_usuario_punto_evaluacion->punto_evaluacion()->associate($punto_evaluacion);
            $evaluacion_usuario_punto_evaluacion->save();
            $evaluacion_usuario_punto_evaluacion->valor_item_punto_evaluacion()->sync($request->input('vae.*'));

            $punto_evaluacion->ponderaItems();
            $punto_evaluacion->ponderaFinal();

            $evaluacion_usuario_punto_evaluacion->asociar_imagenes(collect($request->input('ima_img')));

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error interno'
            ], 500);
        }

        return $this->respond('La evaluación ha sido ingresada');
    }


    /**
     * Actualiza evaluación de un punto de evaluación
     *
     * PATCH
     *
     * @param  Request $request Información de la evalauación a actualizar
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function evaluate_update(Request $request, PuntoEvaluacion $punto_evaluacion)
    {
        $validator = Validator::make($request->all(), [
            'vae.*' => 'nullable|in:'.ValorItemPuntoEvaluacion::identificadores_validos(),
            'eup_cmn' => 'nullable|max:255',
            'ima_img.*' => 'nullable|base64image'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 422);
        }

        try{
            $usuario = JWTAuth::toUser();

            $evaluacion_usuario_punto_evaluacion = EvaluacionUsuarioPuntoEvaluacion::where('eup_pev_id', $punto_evaluacion->pev_id)
                                                                                    ->where('eup_usu_id', $usuario->usu_id)
                                                                                    ->first();

            if (empty($evaluacion_usuario_punto_evaluacion)) {
                return response()->json([
                    'message' => 'El modelo no ha sido encontrado'
                ], 422);
            }

            if (!empty($request->input('eup_cmn'))) {
                $evaluacion_usuario_punto_evaluacion->eup_comentario = $request->input('pev_ttl');
            }

            if (!empty($request->input('vae'))) {
                $evaluacion_usuario_punto_evaluacion->valor_item_punto_evaluacion()->sync($request->input('vae.*'));
            }

            $evaluacion_usuario_punto_evaluacion->save();

            if (!empty($request->has('ima_img'))) {
                $evaluacion_usuario_punto_evaluacion->asociar_imagenes(collect($request->input('ima_img')));
            }

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error interno'
            ], 500);
        }

        return $this->respond('La evaluación ha sido actualizada');
    }

    /**
     * Ingresa una nueva votación a un punto de evaluación
     *
     * POST
     *
     * @param  Request $request Información del voto
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function vote(Request $request, PuntoEvaluacion $punto_evaluacion)
    {
        $validator = Validator::make($request->all(), [
            'vot_tpe' => 'required|boolean'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 422);
        }

        try{
            $usuario = JWTAuth::toUser();

            $voto = $request->input('vot_tpe');

            /**
             * consultar si la intención de voto del mismo usuario es la misma o no. en caso de no
             * ser la misma el usuario puede cambiar su voto. si desea votar nuevamente con
             * la misma intención, esta se niega.
             */
            if (!empty($usuario->punto_evaluacion_votacion->find($punto_evaluacion->pev_id))
            ) {
                if ($usuario->punto_evaluacion_votacion->find($punto_evaluacion->pev_id)->pivot->vot_intencion_votacion == $voto) {
                    return $this->respond('El usuario ya ha votado previamente');
                }

                $punto_evaluacion->usuario_votacion()->detach($usuario);
                if ($voto == 1) {
                    $punto_evaluacion->pev_votos_negativos -= 1;
                } else {
                    $punto_evaluacion->pev_votos_positivos -= 1;
                }
            }

            $punto_evaluacion->usuario_votacion()->attach($usuario, [
                                                                    VotacionUsuarioPeval::$field_mask['vot_tpe'] => $voto
                                                        ]);

            if ($voto == 1) {
                $punto_evaluacion->pev_votos_positivos += 1;
            } else {
                $punto_evaluacion->pev_votos_negativos += 1;
            }

            $punto_evaluacion->save();

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error interno'
            ], 500);
        }

        return $this->respond('La votación ha sido ingresada');
    }

    /**
     * Ingresa un nuevo comentario a un punto de evaluación
     *
     * POST
     *
     * @param  Request $request Información del comentario
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function comment(Request $request, PuntoEvaluacion $punto_evaluacion)
    {
        $validator = Validator::make($request->all(), [
            'com_usu_pev' => 'required|string|max:255'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 422);
        }

        try{
            $usuario = JWTAuth::toUser();

            $usuario->punto_evaluacion_comentario()->attach($punto_evaluacion->pev_id, [ComentarioUsuarioPeval::$field_mask['com_usu_pev'] => $request->input('com_usu_pev')]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error interno'
            ], 500);
        }

        return $this->respond('El comentario ha sido ingresado');
    }

    /**
     * Función que entrega set de datos para los dos gráficos de la pantalla gráficos
     * Porcentaje por ponderación buena mala o media para filtros seleccionados,
     * línea de tiempo para los diferentes tipo de evaluaciones realizadas
     *
     *
     * GET [plataforma]
     *
     * @param  $tipo_punto tipo de punto
     * @param  $sector identificador del sector
     * @param  $categoria categoría del tipo
     * @param  $tipo_sector tipo de sector a filtrar
     * @param  $item filtro por item de evaluación
     * @return json 			Datos del estacionamiento
     */
    public function tipoPorAccesibilidad($tipo_punto, $sector, $categoria, $tipo_sector, $item = null)
    {
        try{
            $puntos = PuntoEvaluacion::porTipoFiltro(PuntoEvaluacion::$tipo_literal[$tipo_punto], $sector, $categoria, $tipo_sector, $item);

            //tipo accesibilidad
            $puntos_count = $puntos->count();
            $puntos_accesibilidad = [];

            $puntos->groupBy('pev_evaluacion_ponderada', false)
                ->map(function($ponderacion, $key) use ($puntos_count, &$puntos_accesibilidad) {
                    $ponderacion_count = $ponderacion->count();
                    if(in_array($key, ['0','1','2'])) {
                        array_push($puntos_accesibilidad, [
                            'label' => PuntoEvaluacion::$accesibilidad_literal[$key],
                            'data' => round($ponderacion_count * 100 / $puntos_count, 2),
                        ]);
                    }
                });

            //linea de tiempo de evaluación
            $linea_de_tiempo = $puntos->where('created_at', '>', Carbon::now()->subYear(2))->sortBy('created_at')
                ->groupBy('pev_evaluacion_ponderada')
                ->map(function($grupo_puntos_ponderado) {
                    return $grupo_puntos_ponderado->groupBy(function ($punto) {
                        return $punto->created_at->month.' '.$punto->created_at->year;
                    });
                })
                ->map(function($grupo_puntos_ponderado) {
                    return $grupo_puntos_ponderado->map(function($grupo_puntos_mes, $mes){
                        //se multiplica el valor por 1000 para coincidir con el formato JS
                        return [\DateTime::createFromFormat('m Y', $mes)->format('U') * 1000, $grupo_puntos_mes->count()];
                    })->values();
                });

            return response()->json([
                'tipo_accesibilidad' => $puntos_accesibilidad,
                'linea_tiempo' => $linea_de_tiempo,
                'count' => $puntos_count
            ]);


        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }

    /**
     * Función que entrega set de datos para los gráficos de la pantalla reporte
     * Nombre de la Región y su porcentaje por ponderación buena mala o media
     * para filtros seleccionados.
     *
     *
     *
     * GET [plataforma]
     *
     * @param  $tipo_punto tipo de punto
     * @param  $sector identificador del sector
     * @param  $categoria categoría del tipo
     * @param  $tipo_sector tipo de sector a filtrar
     * @return json 			Datos buscados
     */
    public function tipoPorAccesibilidadRegiones($tipo_punto, $sector, $categoria, $tipo_sector){
      try {
        $datos = [];

        switch ($tipo_sector) {
          case 1:
            switch ($sector) {
              case 0:
                foreach (Region::cursor() as $i => $j) {
                    $puntos = $this->tipoPorAccesibilidad($tipo_punto, $j->reg_id, $categoria, 1, null)->getData();
                    array_push($datos, ['nombre' => $j->reg_nombre, 'puntos' => $puntos->tipo_accesibilidad]);
                }
                break;

              default:
                $region = Region::find($sector);

                $puntos = $this->tipoPorAccesibilidad($tipo_punto, $sector, $categoria, 1, null)->getData();

                array_push($datos, ['nombre' => $region->reg_nombre, 'puntos' => $puntos->tipo_accesibilidad]);
                break;
            }
            break;

          case 2:
            switch ($sector) {
              case 0:
                foreach (Region::cursor() as $i => $j) {
                    $puntos = $this->tipoPorAccesibilidad($tipo_punto, $j->reg_id, $categoria, 1, null)->getData();
                    array_push($datos, ['nombre' => $j->reg_nombre, 'puntos' => $puntos->tipo_accesibilidad]);
                }
                break;

              default:
                $region = Comuna::find($sector)->provincia->region;
                /*$datos = [];
                $regiones = PuntoEvaluacion::porRegionEvaluacion(PuntoEvaluacion::$tipo_literal[$tipo_punto], $tipo_sector,  $region->reg_id , $categoria );
                $regiones->map(function($comuna, $key) use (&$datos){
                    $puntos_accesibilidad = [];
                    $puntos_count = $comuna->punto_evaluacion->count();
                    $comuna->punto_evaluacion->groupBy('pev_evaluacion_ponderada', false)
                      ->map(function($puntos_eval, $key_p) use(&$puntos_accesibilidad, $puntos_count){
                        $ponderacion_count = $puntos_eval->count();
                        array_push($puntos_accesibilidad, [
                          'label' => PuntoEvaluacion::$accesibilidad_literal[$key_p],
                          'data' => round($ponderacion_count * 100 / $puntos_count, 2)
                        ]);
                    });
                    array_push($datos,[
                      'nombre' => $comuna->com_nombre,
                      'puntos' => $puntos_accesibilidad
                    ]);
                });
                return $datos;
                dd($regiones);*/

                $puntos = $this->tipoPorAccesibilidad($tipo_punto, $region->reg_id, $categoria, 1, null)->getData();

                array_push($datos, ['nombre' => $region->reg_nombre, 'puntos' => $puntos->tipo_accesibilidad]);
                break;
                break;
            }
            break;
        }

        return $datos;

      } catch (Exception $e) {
        return $this->respond([
            'message' => 'Error interno'
        ]);
      }

    }

    /**
     * Función que entrega set de datos para los gráficos de la pantalla reporte
     * Nombre de la comuna y su porcentaje por ponderación buena mala o media
     * para filtros seleccionados.
     *
     *
     *
     * GET [plataforma]
     *
     * @param  $tipo_punto tipo de punto
     * @param  $sector identificador del sector
     * @param  $categoria categoría del tipo
     * @param  $tipo_sector tipo de sector a filtrar
     * @return json 			Datos buscados
     */
    public function tipoPorAccesibilidadComunas($tipo_punto, $sector, $categoria, $tipo_sector){
      try {

        $datos = [];

        $comunas = PuntoEvaluacion::porComunaEvaluacion(PuntoEvaluacion::$tipo_literal[$tipo_punto], $tipo_sector, $sector, $categoria);
        $comunas->map(function($comuna, $key) use (&$datos){
            $puntos_accesibilidad = [];
            $puntos_count = $comuna->punto_evaluacion->count();
            $comuna->punto_evaluacion->groupBy('pev_evaluacion_ponderada', false)
              ->map(function($puntos_eval, $key_p) use(&$puntos_accesibilidad, $puntos_count){
                $ponderacion_count = $puntos_eval->count();
                array_push($puntos_accesibilidad, [
                  'label' => PuntoEvaluacion::$accesibilidad_literal[$key_p],
                  'data' => round($ponderacion_count * 100 / $puntos_count, 2)
                ]);
            });
            array_push($datos,[
              'nombre' => $comuna->com_nombre,
              'puntos' => $puntos_accesibilidad
            ]);
        });

        return $datos;

      } catch (Exception $e) {
        return $this->respond([
            'message' => 'Error interno'
        ]);
      }
    }

    public function paraExcel($tipo_punto, $sector, $categoria, $tipo_sector){
      try {

        $edificaciones = PuntoEvaluacion::paraFiltroExcel(PuntoEvaluacion::$tipo_literal[$tipo_punto], $sector, $categoria, $tipo_sector);

        switch ($tipo_punto) {
          case 2:
            $item = ItemEvaluar::where('iev_nombre_dato', 'like', 'edi%')->select('iev_descripcion')->get();
            break;
          case 3:
            $item = ItemEvaluar::where('iev_nombre_dato', 'like', 'esp%')->select('iev_descripcion')->get();
            break;
          case 4:
            $item = ItemEvaluar::where('iev_nombre_dato', 'like', 'ver%')->select('iev_descripcion')->get();
            break;
        }

        return response()->json([$edificaciones, $item]);

      } catch (Exception $e) {
        return $this->respond([
            'message' => 'Error interno'
        ]);
      }

    }
}
