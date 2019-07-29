<?php

namespace App\Http\Controllers;

use App\PuntoEvaluacion;
use App\Estacionamiento;
use App\TipoPuntoEvaluacion;
use App\Comuna;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use League\Fractal;
use App\Transformers\EstacionamientoTransformer;
use League\Fractal\Manager;

class EstacionamientoController extends ApiController
{
	/**
     * Ingresa un nuevo punto de evaluación estacionamiento
     *
     * POST
     * 
     * @param  Request $request Información del punto y del estacionamiento
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function create(Request $request)
    {
    	$validator = Validator::make($request->all(), [
			'est_cnt_ttl' => 'required|integer',
	    	'est_cnt_rsv' => 'required|integer',
	    	'est_cmp_ord' => 'required|boolean',
	    	'est_cmn' => 'string|max:255|nullable',
	    	'pev_ttl' => 'required|string|max:255',
	        'pev_ltt' => 'required|numeric|between:-90,90',
	        'pev_lng' => 'required|numeric|between:-180,180',
	        'ime_img.*' => 'required|base64image'
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

            $tipo_punto_evaluacion = TipoPuntoEvaluacion::find(1);
            $punto_evaluacion->tipo_punto_evaluacion()->associate($tipo_punto_evaluacion);

			$punto_evaluacion->usuario()->associate(JWTAuth::toUser());

			if ($comuna = Comuna::fromLongLat($request->input('pev_lng'), $request->input('pev_ltt'))) {
				$punto_evaluacion->comuna()->associate($comuna);
			}

			$punto_evaluacion->save();
		        
			$estacionamiento = new Estacionamiento([
							Estacionamiento::$field_mask['est_cnt_ttl'] => $request->input('est_cnt_ttl'),
							Estacionamiento::$field_mask['est_cnt_rsv'] => $request->input('est_cnt_rsv'),
							Estacionamiento::$field_mask['est_cmp_ord'] => $request->input('est_cmp_ord'),
							Estacionamiento::$field_mask['est_cmn'] => $request->input('est_cmn')
						]);
			
			$estacionamiento->punto_evaluacion()->associate($punto_evaluacion);
			$estacionamiento->save();

			if ($request->has('ime_img')) {
				$estacionamiento->asociar_imagenes(collect($request->input('ime_img')));
			}

		} catch (Exception $e) {
			return response()->json([
				'message' => 'Error interno'
			], 500);
		}

		return $this->respond('El estacionamiento ha sido ingresado');
    }

    /**
     * Actualiza estacionamiento
     *
     * PATCH
     * 
     * @param  Request $request Identificador y datos a modificar del estacionameinto
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function update(Request $request, PuntoEvaluacion $punto_evaluacion)
    {
    	$validator = Validator::make($request->all(), [
			'est_cnt_ttl' => 'integer|nullable',
	    	'est_cnt_rsv' => 'integer|nullable',
	    	'est_cmp_ord' => 'boolean|nullable',
	    	'est_cmn' => 'string|max:255|nullable',
	    	'pev_ttl' => 'string|max:255|nullable',
	        'ime_img.*' => 'base64image|nullable'
		]);

		if ($validator->fails()){
			return response()->json([
				'message' => $validator->messages()
		   	], 422);
		}

		try{
		    $estacionamiento = $punto_evaluacion->estacionamiento;

			if (!empty($request->input('pev_ttl'))) {
				$punto_evaluacion->pev_titulo = $request->input('pev_ttl');
				$punto_evaluacion->save();
			}

			if (!empty($request->input('ime_img'))) {
				$estacionamiento->asociar_imagenes(collect($request->input('ime_img')));
			}

			collect($request->only(['est_cnt_ttl', 'est_cnt_rsv', 'est_cmp_ord', 'est_cmn']))->filter()->map(function($input, $key) use ($estacionamiento){
                return $estacionamiento->{Estacionamiento::$field_mask[$key]} = $input;
            });

			$estacionamiento->save();

		} catch (Exception $e) {
			return response()->json([
				'message' => 'Error interno'
			], 500);
		}

		return $this->respond('El estacionamiento ha sido actualizado');
    }

    /**
     * Elimina un estacionamiento
     *
     * DELETE
     * 
     * @param  Request $request Identificador del estacionameinto
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

		return $this->respond('El estacionamiento ha sido eliminado');
    }

    /**
     * Obtiene los datos de un estacionamiento
     *
     * GET
     * 
     * @param  Request $request Identificador del estacionameinto
     * @return json 			Datos del estacionamiento
     */
    public function show(PuntoEvaluacion $punto_evaluacion)
    {
		try{
            $estacionamiento = $punto_evaluacion->estacionamiento;

            $resource = new Fractal\Resource\Item($estacionamiento, new EstacionamientoTransformer());
            $fractal = new Manager();
            $data = $fractal->createData($resource)->toArray();

            return $this->respond('Datos de estacionamiento', $data);

		} catch (Exception $e) {
			return response()->json([
				'message' => 'Error interno'
			], 500);
		}
    }
}
