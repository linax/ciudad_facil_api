<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;

class authJWT
{
    public function handle($request, Closure $next)
    {   
        //if (App::environment('local')) return $next($request); 

        $header = array (
                'Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'
            );
        try {
            $user = JWTAuth::toUser($request->header('Authorization'));
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'message' => 'Token inválido'
                ], 401, $header, JSON_UNESCAPED_UNICODE);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    'message' => 'El token ha expirado'
                ], 401, $header, JSON_UNESCAPED_UNICODE);
            }else{
                return response()->json([
                    'message' => 'Error: verificar proceso de autenticación'
                ], 500, $header, JSON_UNESCAPED_UNICODE);
            }
        }
        
        return $next($request);
    }
}
