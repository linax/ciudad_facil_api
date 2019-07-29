<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UsuarioSocialService;
use JWTAuth;
use Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider) 
    {
    	return Socialite::driver($provider)->redirect();
    }

    public function callback(UsuarioSocialService $service, $provider)
    {
    	$usuario = $service->createOrGetUser(Socialite::driver($provider));

        $respuesta = collect([
            'created' => isset($usuario->usu_discapacidad),
            'token' => JWTAuth::fromUser($usuario),
            'name' => $usuario->usuario_social()->first()->uss_nombre_completo
        ])->toJson(JSON_UNESCAPED_UNICODE);

        return view('auth.social', compact('respuesta'));
    }
}
