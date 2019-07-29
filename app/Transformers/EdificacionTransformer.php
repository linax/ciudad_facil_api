<?php

namespace App\Transformers;

use App\ComentarioUsuarioPeval;
use App\Edificacion;
use League\Fractal\TransformerAbstract;
use App\EvaluacionUsuarioPuntoEvaluacion;
use JWTAuth;

class EdificacionTransformer extends TransformerAbstract {

    public function transform(Edificacion $edificacion)
    {
        $punto_edificacion = $edificacion->punto_evaluacion;

        return [
            'edi_dir' => $edificacion->edi_direccion,
            'edi_ema' => $edificacion->edi_email,
            'edi_pub' => $edificacion->edi_publico,
            'edi_tel' => $edificacion->edi_telefono,
            'edi_cat' => $edificacion->subcategoria_edificacion->categoria_edificacion->cat_nombre,
            'edi_sub_cat' => $edificacion->subcategoria_edificacion->sub_nombre,
            'pev_ttl' => $punto_edificacion->pev_titulo,
            'pev_ltt' => $punto_edificacion->pev_latitud,
            'pev_lng' => $punto_edificacion->pev_longitud,
            'pev_pos' => $punto_edificacion->pev_votos_positivos,
            'pev_neg' => $punto_edificacion->pev_votos_negativos,
            'pev_usu_nbr' => $punto_edificacion->usuario->usu_nombre_completo,
            'evp_eva' => $punto_edificacion->pev_evaluacion_items,
            'evp_eva_fin' => $punto_edificacion->pev_evaluacion_ponderada,
            'evals' => $punto_edificacion->evaluacion_usuario_punto_evaluacion->map(function($evaluacion_usuario_punto_evaluacion){
                return [
                    'eup_id' => $evaluacion_usuario_punto_evaluacion->eup_id,
                    'usr_evl' => $evaluacion_usuario_punto_evaluacion->usuario->usu_nombres,
                    'eup_cmn' => $evaluacion_usuario_punto_evaluacion->eup_comentario,
                    'vae' => $evaluacion_usuario_punto_evaluacion->valor_item_punto_evaluacion()->get()->map(function($valor_item_punto_evaluacion){
                        return [
                            'vie_id' => $valor_item_punto_evaluacion->vie_id,
                            'vie_dsc' => $valor_item_punto_evaluacion->vie_descripcion
                        ];
                    })->sortBy('vie_orden')->values()->all()/*pluck('vie_descripcion')*/,
                    'ema_img' => $evaluacion_usuario_punto_evaluacion->ultima_imagen(),
                    'eup_created_at' => $evaluacion_usuario_punto_evaluacion->created_at->format('d/m/Y H:i:s')
                ];
            })->sortByDesc('created_at')->values()->all(),
            'com_usu_pev' => ComentarioUsuarioPeval::where('com_pev_id', $punto_edificacion->pev_id)->get()->map(function($comentario){
                return [
                    'com_usu' => $comentario->usuario->usu_nombres,
                    'com_cmn' => $comentario->com_comentario,
                    'com_created_at' => $comentario->created_at->format('d/m/Y H:i:s')
                ];
            })->sortByDesc('created_at')->values()->all(),
            'permissions' => [
                'u' => ($punto_edificacion->usuario == JWTAuth::toUser()),
                'd' => ($punto_edificacion->usuario == JWTAuth::toUser()),
                'e' => $punto_edificacion->editable(),
                'e_eval' => ($evaluacion = EvaluacionUsuarioPuntoEvaluacion::where('eup_usu_id', JWTAuth::toUser()->usu_id)->where('eup_pev_id', $punto_edificacion->pev_id)->first()) ? $evaluacion->eup_id : null
            ]
        ];
    }

}