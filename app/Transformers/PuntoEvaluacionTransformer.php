<?php 

namespace App\Transformers;

use App\PuntoEvaluacion;
use League\Fractal\TransformerAbstract;
use JWTAuth;

class PuntoEvaluacionTransformer extends TransformerAbstract {

    public function transform(PuntoEvaluacion $punto_evaluacion)
    {
        return [
            'evp_id' => $punto_evaluacion->pev_id,
            'evp_nam' => $punto_evaluacion->pev_titulo,
            'evp_typ' => $punto_evaluacion->pev_tpe_id,
            'evp_lat' => $punto_evaluacion->pev_latitud,
            'evp_lon' => $punto_evaluacion->pev_longitud,
            'evp_eva' => $punto_evaluacion->pev_evaluacion_items,
            'evp_eva_fin' => $punto_evaluacion->pev_evaluacion_ponderada
        ];
    }

}