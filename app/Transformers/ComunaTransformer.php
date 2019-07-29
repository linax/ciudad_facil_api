<?php

namespace App\Transformers;

use App\ComentarioUsuarioPeval;
use App\Comuna;
use League\Fractal\TransformerAbstract;
use JWTAuth;

class ComunaTransformer extends TransformerAbstract {

    public function transform(Comuna $comuna)
    {
        //$punto_edificacion = $edificacion->punto_evaluacion;

        return [
            'com_id' => $comuna->com_id,
            'com_nom'=>$comuna->com_nombre
         
        ];
    }

}