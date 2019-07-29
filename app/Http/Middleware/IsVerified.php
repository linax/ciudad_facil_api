<?php

namespace App\Http\Middleware;

use Closure;
use App\Usuario;
use Jrean\UserVerification\Exceptions\UserNotVerifiedException;

class IsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws Jrean\UserVerification\Exceptions\UserNotVerifiedException
     */
    public function handle($request, Closure $next)
    {
        $usuario = Usuario::where('email', $request->input('usu_eml'))->first();

        if( !empty($usuario) && !$usuario->isVerified() ){
            return response()->json([
                'message' => 'Usuario no verificado, consulte su correo'
                ], 401);
        }

        return $next($request);
    }
}