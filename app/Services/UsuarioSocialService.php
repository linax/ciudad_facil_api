<?php

namespace App\Services;

use Laravel\Socialite\Contracts\Provider;
use App\UsuarioSocial;
use App\Usuario;

class UsuarioSocialService
{
    public function createOrGetUser(Provider $provider)
    {   
        $provider_user = $provider->user();
        $provider_name = class_basename($provider); 

        $account = UsuarioSocial::where('uss_proveedor', $provider_name)
                                ->where('uss_proveedor_usu_id', $provider_user->getId())
                                ->first();

        if ($account) {
            $account->uss_nombre_completo = $provider_user->getName();
            $account->save();
            return $account->usuario;
        } else {

            $account = new UsuarioSocial([
                'uss_proveedor_usu_id' => $provider_user->getId(),
                'uss_proveedor' => $provider_name,
                'uss_nombre_completo' => $provider_user->getName()
            ]);

            $usuario = Usuario::whereEmail($provider_user->getEmail())->first();

            if (!$usuario) {
                $usuario = Usuario::create([
                    'email' => $provider_user->getEmail(),
                    'verified' => true,
                ]);
            }

            $account->usuario()->associate($usuario);
            $account->save();

            return $usuario;

        }

    }
}
