<?php

namespace App\Http\Controllers;

use App\SubcategoriaEspacioPublico;
use App\PuntoEvaluacion;
use App\EvaluacionUsuarioPuntoEvaluacion;
use App\TipoPuntoEvaluacion;
use App\Comuna;
use Illuminate\Http\Request;
use App\EspacioPublico;
use League\Fractal;
use App\Transformers\EspacioPublicoTransformer;
use League\Fractal\Manager;
use Validator;
use JWTAuth;
use App\ValorItemPuntoEvaluacion;


class EspacioPublicoController extends ApiController
{
    /**
     * Ingresa un nuevo punto de espacio público[puntos tipo 2]
     *
     * POST
     *
     * @param  Request $request Información del punto y de la edificación
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'esp_dir' => 'string|max:255|nullable',
            'esp_ema' => 'string|max:255|nullable',
            'esp_tel' => 'string|max:255|nullable',
            'esp_sub_cat' => 'required|in:'.SubcategoriaEspacioPublico::identificadores_validos(),
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

            $tipo_punto_evaluacion = TipoPuntoEvaluacion::find(3);
            $punto_evaluacion->tipo_punto_evaluacion()->associate($tipo_punto_evaluacion);

            $usuario = JWTAuth::toUser();
            $punto_evaluacion->usuario()->associate($usuario);

            if ($comuna = Comuna::fromLongLat($request->input('pev_lng'), $request->input('pev_ltt'))) {
                $punto_evaluacion->comuna()->associate($comuna);
            }

            $punto_evaluacion->save();

            $espacio_publico = new EspacioPublico([
                EspacioPublico::$field_mask['esp_dir'] => $request->input('esp_dir'),
                EspacioPublico::$field_mask['esp_ema'] => $request->input('esp_ema'),
                EspacioPublico::$field_mask['esp_tel'] => $request->input('esp_tel')
            ]);

            $espacio_publico->punto_evaluacion()->associate($punto_evaluacion);

            $subcategoria_espacio_publico = SubcategoriaEspacioPublico::find($request->input('esp_sub_cat'));
            $espacio_publico->subcategoria_espacio_publico()->associate($subcategoria_espacio_publico);

            $espacio_publico->save();

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
            'message' => 'El espacio público ha sido ingresado'
        ], 200, $header);
    }

    /**
     * Actualiza espacio público
     *
     * PATCH
     *
     * @param  Request $request Identificador y datos a modificar del espacio público
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function update(Request $request, PuntoEvaluacion $punto_evaluacion)
    {
        $validator = Validator::make($request->all(), [
            'esp_dir' => 'string|max:255|nullable',
            'esp_ema' => 'string|max:255|nullable',
            'esp_tel' => 'string|max:255|nullable',
            'esp_sub_cat' => 'in:'.SubcategoriaEspacioPublico::identificadores_validos().'|nullable',
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

            $espacio_publico = $punto_evaluacion->espacio_publico;

            collect($request->only(['esp_dir', 'esp_ema', 'esp_tel']))->filter()->map(function($input, $key) use ($espacio_publico){
                return $espacio_publico->{EspacioPublico::$field_mask[$key]} = $input;
            });

            if (!empty($request->input('esp_sub_cat'))){
                $subcategoria_espacio_publico = SubcategoriaEspacioPublico::find($request->input('esp_sub_cat'));
                $espacio_publico->subcategoria_espacio_publico()->associate($subcategoria_espacio_publico);
            }

            $espacio_publico->save();

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

        $header = array (
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        return response()->json([
            'message' => 'El espacio público ha sido actualizado'
        ], 200, $header);
    }

    /**
     * Elimina un espacio público
     *
     * DELETE
     *
     * @param PuntoEvaluacion $punto_evaluacion
     * @return json Mensaje de aprobación o errores de validación
     * @internal param Request $request Identificador del espacio público
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

        return $this->respond('El espacio público ha sido eliminado');
    }

    /**
     * Obtiene los datos de un espacio público
     *
     * GET
     *
     * @param PuntoEvaluacion $punto_evaluacion
     * @return json Datos del espacio público
     * @internal param Request $request Identificador del punto espaico público
     */
    public function show(PuntoEvaluacion $punto_evaluacion)
    {
        try{
            $espacio_publico = $punto_evaluacion->espacio_publico;

            $resource = new Fractal\Resource\Item($espacio_publico, new EspacioPublicoTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();

            return $this->respond('Datos de espacio público', $data);

        } catch (Exception $e) {
            return $this->respond([
                'message' => 'Error interno'
            ]);
        }
    }
}
