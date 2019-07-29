<?php

namespace App\Transformers;

use App\Region;
use League\Fractal\TransformerAbstract;
use JWTAuth;

class RegionTransformer extends TransformerAbstract {

    public function transform(Region $regiones)
    {
       // $provincia = $regiones->provincia;
        return [
            'reg_id' => $regiones->reg_id,
            'reg_nom' => $regiones->reg_nombre,
            /*'comunas' => $provincia->map(function($provincia){
                return (
                     $provincia->comuna->map(function($comuna){
                        return [
                        'com_id' => $comuna->com_id,
                        'com_nom' => $comuna->com_nombre
                        ];
                    })->sortByDesc('com_orden')->values()->all()
                );
            })->sortByDesc('pro_orden')->values()->all()*/
        ];
    }

}