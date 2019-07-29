<?php

use Illuminate\Http\Request;


/**
 * Rutas disponibles durante testing
 */
Route::get('remueve_usuarios', 'UsuarioController@delete_all');
Route::patch('social_update', 'UsuarioController@social_update');

Route::get('set_regions', function(){
	App\PuntoEvaluacion::chunk(5, function($puntos_evaluacion){
		$puntos_evaluacion->map(function($punto){
			if ($comuna = App\Comuna::fromLongLat($punto->pev_longitud, $punto->pev_latitud)) {
				$punto->comuna()->associate($comuna);
				$punto->save();
				return true;
			}
		});
	});
	return 'true';
});

/**
 * Password reset
 *
Route::group(['prefix' => 'password'], function(){

	Route::post('email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
	Route::get('reset', 'Auth\ForgotPasswordController@showLinkRequestForm');

	Route::get('reset/{token}', 'Auth\ResetPasswordController@showResetForm');
	Route::post('reset', 'Auth\ResetPasswordController@reset');
});*/

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/**
 * Rutas liberadas
 */
Route::post('user', 'UsuarioController@register');

/**
 * Routes for resource version-app-controller
 */
Route::get('version', 'VersionAppController@show');

/**
 * Verificación de email
 *
Route::group(['prefix' => 'email-verification'], function(){

	Route::get('error', 'Auth\RegisterController@getVerificationError')->name('email-verification.error');
	Route::get('check/{token}', 'Auth\RegisterController@getVerification')->name('email-verification.check');

});*/

/**
 * Login protegido por verificación de ruta
 */
Route::group(['middleware' => 'isVerified'], function(){

	Route::post('login', 'ApiController@login');

});

/**
 * Rutas parcialmente protegidas por key header
 */
Route::group(['middleware' => 'key'], function(){

	Route::get('exists_user', 'UsuarioController@exists_user');

});

/*
 * Retorno de todos los puntos de evaluación
 */
Route::get('evaluation_point', 'PuntoEvaluacionController@show');


/**
 * Rutas protegidas por login mediante JWT
 */
Route::group(['middleware' => ['api', 'jwt-auth']], function () {

    /**
	 * Routes for resource usuario
	 **/
    Route::get('user', 'UsuarioController@show');
    Route::patch('user', 'UsuarioController@update');
    Route::patch('user/image', 'UsuarioController@update_image');
    Route::get('user/evaluation_point', 'UsuarioController@show_evaluation_points');

    /**
	 * Routes for resource punto-evaluacion
	 */
    Route::post('evaluation_point/{punto_evaluacion}/evaluate', 'PuntoEvaluacionController@evaluate');
    Route::patch('evaluation_point/{punto_evaluacion}/evaluate', 'PuntoEvaluacionController@evaluate_update');
	Route::post('evaluation_point/{punto_evaluacion}/vote', 'PuntoEvaluacionController@vote');
    Route::post('evaluation_point/{punto_evaluacion}/comment', 'PuntoEvaluacionController@comment');
    Route::get('evaluation_point/type/{tipo_punto_evaluacion}/evaluation_alternatives', 'TipoPuntoEvaluacionController@show');

    /**
	 * Routes for resource estacionamiento
	 */
    Route::post('parking', 'EstacionamientoController@create');
    Route::patch('parking/{punto_evaluacion}', 'EstacionamientoController@update');
    Route::delete('parking/{punto_evaluacion}', 'EstacionamientoController@delete');
    Route::get('parking/{punto_evaluacion}', 'EstacionamientoController@show');

    /**
     * Routes for resource edificacion
     */
    Route::post('building', 'EdificacionController@create');
    Route::patch('building/{punto_evaluacion}', 'EdificacionController@update');
    Route::delete('building/{punto_evaluacion}', 'EdificacionController@delete');
    Route::get('building/{punto_evaluacion}', 'EdificacionController@show');
    Route::get('categories/public_space', 'CategoriaEspacioPublicoController@show');

    /**
     * Routes for espacio público
     */
    Route::post('public_space', 'EspacioPublicoController@create');
    Route::patch('public_space/{punto_evaluacion}', 'EspacioPublicoController@update');
    Route::delete('public_space/{punto_evaluacion}', 'EspacioPublicoController@delete');
    Route::get('public_space/{punto_evaluacion}', 'EspacioPublicoController@show');

    /**
     * Routes for vereda cruce
     */
    Route::post('sidewalk_intersection', 'VeredaCruceController@create');
    Route::patch('sidewalk_intersection/{punto_evaluacion}', 'VeredaCruceController@update');
    Route::delete('sidewalk_intersection/{punto_evaluacion}', 'VeredaCruceController@delete');
    Route::get('sidewalk_intersection/{punto_evaluacion}', 'VeredaCruceController@show');
});


Route::group(['middleware' => ['api', 'plataforma']], function () {
    /**
     * Routes for resource region and comuna
     **/
    Route::get('commune', 'ComunaController@all');
    Route::get('region', 'RegionController@all');
    Route::get('ten_communes', 'ComunaController@tasaAccesibilidad');
    Route::get('evals_regions', 'RegionController@conteoEvaluaciones');
    Route::get('region_commune_accesible', 'ComunaController@region_comuna');

    Route::get('categories/building', 'CategoriaEdificacionController@show');
    Route::get('categories/public_space', 'CategoriaEspacioPublicoController@show');
    Route::get('categories/sidewalk', 'CategoriaVeredaCruceController@show');

    Route::get('eval_excel/{tipo_punto}/{sector}/{categoria}/{tipo_sector}', 'PuntoEvaluacionController@paraExcel');
    Route::get('region/{tipo_punto}/{sector}/{categoria}/{tipo_sector}', 'PuntoEvaluacionController@tipoPorAccesibilidadRegiones');
    Route::get('comuna/{tipo_punto}/{sector}/{categoria}/{tipo_sector}', 'PuntoEvaluacionController@tipoPorAccesibilidadComunas');
    Route::get('{tipo_punto}/{sector}/{categoria}/{tipo_sector}/{item?}', 'PuntoEvaluacionController@tipoPorAccesibilidad');

    Route::get('evals_regions_communes', 'ComunaController@region_comuna_mas_evals');

    /*Route::get('three_regions', 'RegionController@tres_regiones');*/

    Route::get('variable/{tipo}', 'ItemEvaluarController@itemsEval');

});
