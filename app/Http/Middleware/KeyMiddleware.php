<?php

namespace App\Http\Middleware;

use Closure;

class KeyMiddleware
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
        if(empty($request->header('key')) || $request->header('key') != 'ciudadfacil'){
            return response()->json([
                'message' => 'No Autorizadossss'
                ], 401);
        }

        return $next($request);
    }
}
