<?php

namespace App\Transformers;

use App\Usuario;
use League\Fractal\TransformerAbstract;

class UsuarioTransformer extends TransformerAbstract {

    public function transform(Usuario $usuario)
    {
        return [
            'usu_eml' => $usuario->email,        //id
            'usu_nbr_usr' => $usuario->usu_nombre_usuario,   //título o nombre
            'usu_nmb' => $usuario->usu_nombres, //tipo
            'usu_apl_ptr' => $usuario->usu_apellido_paterno,  //latitud
            'usu_apl_mtr' => $usuario->usu_apellido_materno, //longitud
            'usu_tpo_dsc' => $usuario->usu_tipo_discapacidad, //evaluación final
            'usu_img' => (file_exists($usuario->usu_imagen))? 'data:image/' . pathinfo($usuario->usu_imagen, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($usuario->usu_imagen)) : null
        ];
    }

}