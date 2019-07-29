<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;

class AuthController extends Controller
{
	/**
	 * Constructor del controlador para obtener el usuario que está autenticado con JWT
	 * @param Request $request para obtener JWT del header
	 */
    public function __construct(Request $request)
    {
    	//$this->usuario = JWTAuth::toUser($request->header('Authorization'));
    }
}
