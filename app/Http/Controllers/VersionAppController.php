<?php 

namespace App\Http\Controllers;

use App\VersionApp;
use Illuminate\Http\Request;

class VersionAppController extends Controller 
{
	/**
     * Obtiene la versión de la aplicación con la última fecha 
     *
     * GET
     * 
     * @param  Request $request 
     * @return json Versión de la aplicación
     */
    public function show(Request $request)
	{
		$version = VersionApp::orderBy('ver_fecha_app', 'desc')->first();

		$header = array (
                'Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'
            );

		return response()->json([
				'data' => [
                    'version' => $version->ver_version_app
                    ],
				'message' => 'Versión de la aplicación Ciudad Fácil'
			], 200, $header, JSON_UNESCAPED_UNICODE);
	} 

}
