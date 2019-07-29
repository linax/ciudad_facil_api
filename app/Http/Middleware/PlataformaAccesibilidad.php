<?php

namespace App\Http\Middleware;

use Closure;

class PlataformaAccesibilidad
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('x-authorization') != config('auth.plataforma')){
            return response()->json([
                'message' => 'No Autorizado'
            ], 401);
        }

        return $next($request);
    }
}
