<?php

namespace App\Transformers;

use App\ComentarioUsuarioPeval;
use App\Estacionamiento;
use League\Fractal\TransformerAbstract;
use JWTAuth;

class EstacionamientoTransformer extends TransformerAbstract {

    public function transform(Estacionamiento $estacionamiento)
    {
        $punto_estacionamiento = $estacionamiento->punto_evaluacion;

        return [
            'est_cnt_ttl' => $estacionamiento->est_cantidad_total,
            'est_cnt_rsv' => $estacionamiento->est_cantidad_reservados,
            'est_cmp_ord' => $estacionamiento->est_cumple_ordenanza,
            'est_cmn' => $estacionamiento->est_comentario,
            'pev_ttl' => $punto_estacionamiento->pev_titulo,
            'pev_ltt' => $punto_estacionamiento->pev_latitud,
            'pev_lng' => $punto_estacionamiento->pev_longitud,
            'pev_pos' => $punto_estacionamiento->pev_votos_positivos,
            'pev_neg' => $punto_estacionamiento->pev_votos_negativos,
            'pev_usu_nbr' => $punto_estacionamiento->usuario->usu_nombre_completo,
            'ime_img' => $estacionamiento->ultima_imagen(),
            'com_usu_pev' => ComentarioUsuarioPeval::where('com_pev_id', $punto_estacionamiento->pev_id)->get()->map(function($comentario){
                return [
                    'com_usu' => $comentario->usuario->usu_nombres,
                    'com_cmn' => $comentario->com_comentario,
                    'com_created_at' => $comentario->created_at->format('d/m/Y H:i:s')
                ];
            })->sortByDesc('created_at')->values()->all(),
            'opc_com_usu' => $punto_estacionamiento->opinion_comentario,
            'permissions' => [
                'u' => ($punto_estacionamiento->usuario == JWTAuth::toUser()),
                'd' => ($punto_estacionamiento->usuario == JWTAuth::toUser()),
                'e' => $punto_estacionamiento->editable()
            ]
        ];
    }

}
