<?php

namespace App\Http\Controllers;

use App\Edificacion;
use App\EvaluacionUsuarioPuntoEvaluacion;
use App\SubcategoriaEdificacion;
use App\TipoPuntoEvaluacion;
use App\ValorItemPuntoEvaluacion;
use App\PuntoEvaluacion;
use App\Comuna;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use League\Fractal;
use App\Transformers\EdificacionTransformer;
use League\Fractal\Manager;
use DB;


class EdificacionController extends ApiController
{
    /**
     * Ingresa un nuevo punto de evaluación edificación [puntos tipo 2]
     *
     * POST
     *
     * @param  Request $request Información del punto y de la edificación
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edi_dir' => 'string|max:255|nullable',
            'edi_ema' => 'string|max:255|nullable',
            'edi_pub' => 'required|boolean',
            'edi_tel' => 'string|max:255|nullable',
            'edi_sub_cat' => 'required|in:'.SubcategoriaEdificacion::identificadores_validos(),
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

            $tipo_punto_evaluacion = TipoPuntoEvaluacion::find(2);
            $punto_evaluacion->tipo_punto_evaluacion()->associate($tipo_punto_evaluacion);

            $usuario = JWTAuth::toUser();
            $punto_evaluacion->usuario()->associate($usuario);

            if ($comuna = Comuna::fromLongLat($request->input('pev_lng'), $request->input('pev_ltt'))) {
                $punto_evaluacion->comuna()->associate($comuna);
            }

            $punto_evaluacion->save();

            $edificacion = new Edificacion([
                Edificacion::$field_mask['edi_dir'] => $request->input('edi_dir'),
                Edificacion::$field_mask['edi_ema'] => $request->input('edi_ema'),
                Edificacion::$field_mask['edi_pub'] => $request->input('edi_pub'),
                Edificacion::$field_mask['edi_tel'] => $request->input('edi_tel')
            ]);

            $edificacion->punto_evaluacion()->associate($punto_evaluacion);

            $subcategoria_edificacion = SubcategoriaEdificacion::find($request->input('edi_sub_cat'));
            $edificacion->subcategoria_edificacion()->associate($subcategoria_edificacion);

            $edificacion->save();

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

        $header = array (
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        return response()->json([
            'message' => 'La edificación ha sido ingresada'
        ], 200, $header);
    }

    /**
     * Actualiza edificación
     *
     * PATCH
     *
     * @param  Request $request Identificador y datos a modificar de la edificación
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function update(Request $request, PuntoEvaluacion $punto_evaluacion)
    {
        $validator = Validator::make($request->all(), [
            'edi_dir' => 'string|max:255|nullable',
            'edi_ema' => 'string|max:255|nullable',
            'edi_pub' => 'boolean|nullable',
            'edi_tel' => 'string|max:255|nullable',
            'edi_sub_cat' => 'in:'.SubcategoriaEdificacion::identificadores_validos().'|nullable',
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

            $edificacion = $punto_evaluacion->edificacion;

            collect($request->only(['edi_dir', 'edi_ema', 'edi_pub', 'edi_tel']))->filter()->map(function($input, $key) use ($edificacion){
                return $edificacion->{Edificacion::$field_mask[$key]} = $input;
            });

            if (!empty($request->input('edi_sub_cat'))){
                $subcategoria_edificacion = SubcategoriaEdificacion::find($request->input('edi_sub_cat'));
                $edificacion->subcategoria_edificacion()->associate($subcategoria_edificacion);
            }

            $edificacion->save();

            $evaluacion_usuario_punto_evaluacion = $punto_evaluacion->evaluacion_usuario_punto_evaluacion->where('eup_usu_id', $usuario->usu_id)->first();

            if(!empty($request->input('eup_cmn'))) $evaluacion_usuario_punto_evaluacion->eup_comentario = $request->input('eup_cmn');

            if(!empty($request->input('vae'))){
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

        $header = array (
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        return response()->json([
            'message' => 'La edificación ha sido actualizada'
        ], 200, $header);
    }

    /**
     * Elimina una edificación
     *
     * DELETE
     *
     * @param  Request $request Identificador de la edificación
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

        $header = array (
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        return response()->json([
            'message' => 'La edificación ha sido eliminada'
        ], 200);
    }

    /**
     * Obtiene los datos de una edificacion
     *
     * GET
     *
     * @param  Request $request Identificador de la edificacion
     * @return json 			Datos del estacionamiento
     */
    public function show(PuntoEvaluacion $punto_evaluacion)
    {
        try{
            $edificacion = $punto_evaluacion->edificacion;

            $resource = new Fractal\Resource\Item($edificacion, new EdificacionTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();

            return $this->respond('Datos de edificación', $data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }


}
