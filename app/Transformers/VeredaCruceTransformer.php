<?php

namespace App\Transformers;

use App\EvaluacionUsuarioPuntoEvaluacion;
use App\VeredaCruce;
use App\ComentarioUsuarioPeval;
use League\Fractal\TransformerAbstract;
use JWTAuth;

class VeredaCruceTransformer extends TransformerAbstract {

    public function transform(VeredaCruce $vereda_cruce)
    {
        $punto_evaluacion = $vereda_cruce->punto_evaluacion;
        
        return [
            'vcr_tip' => $vereda_cruce->vcr_tipo,
            'vcr_des' => $vereda_cruce->vcr_descripcion,
            'pev_ttl' => $punto_evaluacion->pev_titulo,
            'pev_ltt' => $punto_evaluacion->pev_latitud,
            'pev_lng' => $punto_evaluacion->pev_longitud,
            'pev_pos' => $punto_evaluacion->pev_votos_positivos,
            'pev_neg' => $punto_evaluacion->pev_votos_negativos,
            'pev_usu_nbr' => $punto_evaluacion->usuario->usu_nombre_completo,
            'evp_eva_fin' => $punto_evaluacion->pev_evaluacion_ponderada,
            'vcr_cmn_snd' => $vereda_cruce->vcr_comentario_senadis,
            'vcr_mjr_snd' => $vereda_cruce->vcr_mejora_senadis,
            'evals' => $punto_evaluacion->evaluacion_usuario_punto_evaluacion->map(function($evaluacion_usuario_punto_evaluacion){
                return [
                    'eup_id' => $evaluacion_usuario_punto_evaluacion->eup_id,
                    'usr_evl' => $evaluacion_usuario_punto_evaluacion->usuario->usu_nombres,
                    'eup_cmn' => $evaluacion_usuario_punto_evaluacion->eup_comentario,
                    'vae' => $evaluacion_usuario_punto_evaluacion->valor_item_punto_evaluacion()->get()->map(function($valor_item_punto_evaluacion){
                        return [
                            'vie_id' => $valor_item_punto_evaluacion->vie_id,
                            'vie_dsc' => $valor_item_punto_evaluacion->vie_descripcion
                        ];
                    })->sortBy('vie_orden')->values()->all(),
                    'ema_img' => $evaluacion_usuario_punto_evaluacion->ultima_imagen(),
                    'eup_created_at' => $evaluacion_usuario_punto_evaluacion->created_at->format('d/m/Y H:i:s'),
                ];
            })->sortByDesc('created_at')->values()->all(),
            'com_usu_pev' => ComentarioUsuarioPeval::where('com_pev_id', $punto_evaluacion->pev_id)->get()->map(function($comentario){
                return [
                    'com_usu' => $comentario->usuario->usu_nombres,
                    'com_cmn' => $comentario->com_comentario,
                    'com_created_at' => $comentario->created_at->format('d/m/Y H:i:s')
                ];
            })->sortByDesc('created_at')->values()->all(),
            'permissions' => [
                'u' => ($punto_evaluacion->usuario == JWTAuth::toUser()),
                'd' => ($punto_evaluacion->usuario == JWTAuth::toUser()),
                'e' => $punto_evaluacion->editable(),
                'e_eval' => ($evaluacion = EvaluacionUsuarioPuntoEvaluacion::where('eup_usu_id', JWTAuth::toUser()->usu_id)->where('eup_pev_id', $punto_evaluacion->pev_id)->first()) ? $evaluacion->eup_id : null
            ]
        ];
    }

}