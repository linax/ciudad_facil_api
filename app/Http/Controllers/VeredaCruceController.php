<?php

namespace App\Http\Controllers;

use App\VeredaCruce;
use Illuminate\Http\Request;
use App\PuntoEvaluacion;
use App\ValorItemPuntoEvaluacion;
use App\TipoPuntoEvaluacion;
use App\Comuna;
use App\EvaluacionUsuarioPuntoEvaluacion;
use Validator;
use JWTAuth;
use League\Fractal\Manager;
use League\Fractal;
use App\Transformers\VeredaCruceTransformer;
use DB;

class VeredaCruceController extends ApiController
{
    /**
     * Ingresa un nuevo punto de vereda o cruce [puntos tipo 4,5]
     *
     * POST
     *
     * @param  Request $request Información del punto y de la edificación
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vcr_tip' => 'required|integer', //1:vereda 2:cruce
            'vcr_des' => 'string|max:255|nullable',
            'pev_ttl' => 'required|string|max:255',
            'pev_ltt' => 'required|numeric|between:-90,90',
            'pev_lng' => 'required|numeric|between:-180,180',
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
            /*Falta definir comuna en punto de evaluacion*/
            $punto_evaluacion = new PuntoEvaluacion([
                PuntoEvaluacion::$field_mask['pev_ttl'] => $request->input('pev_ttl'),
                PuntoEvaluacion::$field_mask['pev_ltt'] => $request->input('pev_ltt'),
                PuntoEvaluacion::$field_mask['pev_lng'] => $request->input('pev_lng'),
            ]);

            /*switch ($request->input('vcr_tip')) {
                case 1:*/
                    $tipo_punto_evaluacion = TipoPuntoEvaluacion::find(4);
                    /*break;
                case 2:
                    $tipo_punto_evaluacion = TipoPuntoEvaluacion::find(5);
                    break;
            }*/
            $punto_evaluacion->tipo_punto_evaluacion()->associate($tipo_punto_evaluacion);

            $usuario = JWTAuth::toUser();
            $punto_evaluacion->usuario()->associate($usuario);

            if ($comuna = Comuna::fromLongLat($request->input('pev_lng'), $request->input('pev_ltt'))) {
                $punto_evaluacion->comuna()->associate($comuna);
            }
            
            $punto_evaluacion->save();

            $vereda_cruce = new VeredaCruce([
                VeredaCruce::$field_mask['vcr_tip'] => $request->input('vcr_tip'),
                VeredaCruce::$field_mask['vcr_des'] => $request->input('vcr_des'),
            ]);

            $vereda_cruce->punto_evaluacion()->associate($punto_evaluacion);

            $vereda_cruce->save();

            $evaluacion_usuario_punto_evaluacion = new EvaluacionUsuarioPuntoEvaluacion([
                EvaluacionUsuarioPuntoEvaluacion::$field_mask['eup_cmn'] => $request->input('eup_cmn')
            ]);
            $evaluacion_usuario_punto_evaluacion->usuario()->associate($usuario);
            $evaluacion_usuario_punto_evaluacion->punto_evaluacion()->associate($punto_evaluacion);
            $evaluacion_usuario_punto_evaluacion->save();
            $evaluacion_usuario_punto_evaluacion->valor_item_punto_evaluacion()->sync($request->input('vae.*'));

            $punto_evaluacion->ponderaItems();
            $punto_evaluacion->ponderaFinal();

            if ($request->has('ima_img')) {
                $evaluacion_usuario_punto_evaluacion->asociar_imagenes(collect($request->input('ima_img')));
            }

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error interno'
            ], 500);
        }
        
        return $this->respond('La vereda-cruce ha sido creada');
    }

    /**
     * Actualiza vereda cruce
     *
     * PATCH
     *
     * @param  Request $request Identificador y datos a modificar de la vereda cruce
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function update(Request $request, PuntoEvaluacion $punto_evaluacion)
    {
        $validator = Validator::make($request->all(), [
            'vcr_des' => 'string|max:255|nullable',
            'pev_ttl' => 'string|max:255|nullable',
            'vae.*' => 'in:'.ValorItemPuntoEvaluacion::identificadores_validos().'|nullable',
            'eup_cmn' => 'string|max:255|nullable',
            'ima_img.*' => 'base64image|nullable'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 422);
        }

        try{
            $usuario = JWTAuth::toUser();

            if (!empty($request->input('pev_ttl'))) {
                $punto_evaluacion->pev_titulo = $request->input('pev_ttl');
                $punto_evaluacion->save();
            }

            $vereda_cruce = $punto_evaluacion->vereda_cruce;

            if(!empty($request->input('vcr_des'))) $vereda_cruce->vcr_descripcion = $request->input('vcr_des');

            $vereda_cruce->save();

            $evaluacion_usuario_punto_evaluacion = $punto_evaluacion->evaluacion_usuario_punto_evaluacion->where('eup_usu_id', $usuario->usu_id)->first();

            if(!empty($request->input('eup_cmn'))) $evaluacion_usuario_punto_evaluacion->eup_comentario = $request->input('eup_cmn');

            if(!empty($request->input('vae'))) {
                $evaluacion_usuario_punto_evaluacion->valor_item_punto_evaluacion()->sync($request->input('vae.*'));
                $punto_evaluacion->ponderaItems();
                $punto_evaluacion->ponderaFinal();
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

        return $this->respond('La vereda-cruce ha sido actualizada');
    }

    /**
     * Elimina la vereda cruce
     *
     * DELETE
     *
     * @param  Request $request Identificador de la vereda cruce
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function delete(PuntoEvaluacion $punto_evaluacion)
    {
        try{
            $punto_evaluacion->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error interno'
            ], 500);
        }

        return $this->respond('La vereda-cruce ha sido eliminada');
    }

    /**
     * Obtiene los datos de un espacio público
     *
     * GET
     *
     * @param  PuntoEvaluacion $punto_evaluacion Identificador del punto espaico público
     * @return json 			Datos del espacio público
     */
    public function show(PuntoEvaluacion $punto_evaluacion)
    {
        try{
            $vereda_cruce = $punto_evaluacion->vereda_cruce;

            $resource = new Fractal\Resource\Item($vereda_cruce, new VeredaCruceTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();

            return $this->respond('Datos de espacio público', $data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }


    /*
    Obtiene datos para mostrar en plataforma, de edificaciones

    */
    public function getData($sector, $categoria, $tipoSector, $item = null)
    {
        try{
            $sub = DB::table('edificacion')
            ->leftjoin('subcategoria_edificacion', 'edificacion.edi_sub_id', '=', 'subcategoria_edificacion.sub_id')
            ->leftjoin('punto_evaluacion', 'edificacion.edi_pev_id', '=', 'punto_evaluacion.pev_id')

            ->select('edificacion.edi_id', 'edificacion.edi_pev_id','punto_evaluacion.pev_titulo', 'punto_evaluacion.pev_evaluacion_ponderada', 'subcategoria_edificacion.sub_nombre', 'comuna.com_id', 'comuna.com_nombre')
            ->where("subcategoria_edificacion.sub_cat_id", $categoria);

            //  Agrega Tipo sector a la consulta
            if ($tipoSector == 1) {  //region
                $sub ->leftjoin('comuna', 'punto_evaluacion.pev_com_id', '=', 'comuna.com_id')
                      ->leftjoin('provincia', 'comuna.com_pro_id', '=', 'provincia.pro_id')
                      ->leftjoin('region', 'provincia.pro_reg_id', '=', 'region.reg_id')
                      ->where('region.reg_id', $sector);
            } elseif ($tipoSector == 2) { //comuna
               $sub ->leftjoin('comuna', 'punto_evaluacion.pev_com_id', '=', 'comuna.com_id')
                    ->where('comuna.com_id', $sector);
            } elseif ($tipoSector == 3 && gettype(json_decode($sector)) != "integer") {  //poligono

                $dataSector =  json_decode($sector)->coordinates[0];
                $poligono = '';

                for ($i=0; $i < sizeof($dataSector); $i++) {
                    $poligono = $poligono. (string) $dataSector[$i][0].' '. (string)$dataSector[$i][1].', ';
                }

                $poligono = substr($poligono, 0, -2);

                $sub ->leftjoin('comuna', 'punto_evaluacion.pev_com_id', '=', 'comuna.com_id')
                     ->whereRaw("ST_Intersects(comuna.geom,ST_Transform(ST_GeomFromText('POLYGON(($poligono))' , 4326), 32719))");
            }

            //Revisa si incluye items o valores de evaluación, en caso contrario toma todos los valores.
            if ($item!= null) {
                     $sub ->leftjoin('evaluacion_usuario_punto_evaluacion', 'punto_evaluacion.pev_id', '=', 'evaluacion_usuario_punto_evaluacion.eup_pev_id')
                      ->leftjoin('valor_evaluacion', 'evaluacion_usuario_punto_evaluacion.eup_id', '=', 'valor_evaluacion.vae_eup_id')
                      ->leftjoin('valor_item_punto_evaluacion', 'valor_evaluacion.vae_vie_id', '=', 'valor_item_punto_evaluacion.vie_id')
                      ->where('valor_item_punto_evaluacion.vie_id', $item);
            }

            /*Calcula todo el resultado*/

            $resultado = DB::table( DB::raw("({$sub->toSql()}) as t") )
                ->mergeBindings($sub)
                ->select(DB::raw(" pev_evaluacion_ponderada as nivel_accesibilidad, count(*) as cantidad, to_char((count(*) * 100.0 / sum(count(*)) OVER ()), 'FM990.00')AS porcentaje"))
                ->groupBy('pev_evaluacion_ponderada')
                ->orderBy('pev_evaluacion_ponderada')
                ->get();

            return response()->json($resultado);
      
          } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }

    }
}
