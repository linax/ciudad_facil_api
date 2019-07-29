<?php 

namespace App\Http\Controllers;

use App\UsuarioSocial;
use Illuminate\Http\Request;
use App\Usuario;
use Validator;
use Auth;
use DB;
use JWTAuth;
use Image;
use Jrean\UserVerification\Traits\VerifiesUsers;
use Jrean\UserVerification\Facades\UserVerification;
use App\Transformers\PuntoEvaluacionTransformer;
use App\Transformers\UsuarioTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\File;

class UsuarioController extends ApiController {

    use VerifiesUsers; 

	/*
		metodo a eliminar, solo para test
	*/
    public function delete_all()
    {
    	DB::statement("SET foreign_key_checks=0");
    	UsuarioSocial::truncate();
    	Usuario::truncate();
    	DB::statement("SET foreign_key_checks=1");

    	return response()->json([
    			'message' => 'todos los usuarios han sido eliminados'
    		],200);
    }

    /**
     * Muestra los datos del usuario autenticado
     *
     * GET
     *
     * @param
     * @return json             datos del usuario autenticado
     */
    public function show()
    {
        $usuario = JWTAuth::toUser();

        $resource = new Item($usuario, new UsuarioTransformer());
        $fractal = new Manager();
        $data = $fractal->createData($resource)->toArray();

        return $this->respond('Datos del usuario' ,$data);
    }

    /**
     * Registra un nuevo usuario en la plataforma mediante usuario/password
     *
     * POST
     * 
     * @param  Request $request Información del usuario proveniente del registro
     * @return json 			Mensaje de aprobación o errores de validación
     */
    public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
		        'usu_eml' => 'required|email|max:255|unique:usuario,email',
				'usu_nbr_usr' => 'required|string|max:255|unique:usuario,usu_nombre_usuario',
		        'usu_pss' => 'required|max:255|confirmed',
				'usu_nmb' => 'required|string|max:255',
				'usu_apl_ptr' => 'required|string|max:255',
				'usu_apl_mtr' => 'string|max:255',
				'usu_dsc' => 'required|boolean|max:4',
				'usu_tpo_dsc' => 'required|integer|max:127'
		]);  

		if ($validator->fails()){
			return response()->json([
				'message' => $validator->messages()
		   	], 422);
		}

		$usuario = Usuario::create([
						Usuario::$field_mask['usu_eml'] => $request->input('usu_eml'),
						Usuario::$field_mask['usu_nbr_usr'] => $request->input('usu_nbr_usr'),
						Usuario::$field_mask['usu_pss'] => app('hash')->make($request->input('usu_pss')),
						Usuario::$field_mask['usu_nmb'] => $request->input('usu_nmb'),
						Usuario::$field_mask['usu_apl_ptr'] => $request->input('usu_apl_ptr'),
						Usuario::$field_mask['usu_apl_mtr'] => $request->input('usu_apl_mtr'),
						Usuario::$field_mask['usu_dsc'] => $request->input('usu_dsc'),
						Usuario::$field_mask['usu_tpo_dsc'] => $request->input('usu_tpo_dsc'),
					]);

		UserVerification::generate($usuario);
		UserVerification::send($usuario, 'Confirmación de email - Ciudad Fácil App');

		return $this->respond('Usuario registrado, valide su correo');
	} 

	/**
	 * Actualizar usuario
	 *
	 * PATCH
	 * 
	 * @param  Request $request Imagen del usuario
	 * @return json 			Mensaje de aprobación o error de validación
	 */
	public function update(Request $request)
	{
		$usuario = JWTAuth::toUser();

		$validator = Validator::make($request->all(), [
		        'usu_eml' => 'email|max:255|unique:usuario,email,'.$usuario->usu_id.',usu_id|nullable',
				'usu_nbr_usr' => 'string|max:255|unique:usuario,usu_nombre_usuario,'.$usuario->usu_id.',usu_id|nullable',
		        'usu_pss' => 'max:255|confirmed|nullable',
				'usu_nmb' => 'string|max:255|nullable',
				'usu_apl_ptr' => 'string|max:255|nullable',
				'usu_apl_mtr' => 'string|max:255|nullable',
				'usu_dsc' => 'boolean|max:4|nullable',
				'usu_tpo_dsc' => 'integer|max:127|nullable'
		]);

		if ($validator->fails()){
			return response()->json([
				'message' => $validator->messages()
		   	], 422);
		}

        collect($request->all())->map(function($input, $key) use ($usuario){
            return ($input !== '') ? $usuario->{Usuario::$field_mask[$key]} = $input : false;
        });
		
        if ($request->input('usu_dsc') === false) {
            $usuario->usu_discapacidad = false;
            $usuario->usu_tipo_discapacidad = null;
        }

		$usuario->save();

		return $this->respond('Usuario actualizado');
	}

	/**
	 * Actualizar imagen de usuario
	 *
	 * PUT
	 * 
	 * @param  Request $request Imagen a actualizar
	 * @return json 			Mensaje de aprobación o error de validación
	 */
	public function update_image(Request $request)
	{
		$usuario = JWTAuth::toUser();

		$validator = Validator::make($request->all(), [
	        'usu_img' => 'required|base64image'
		]);

		if ($validator->fails()){
			return response()->json([
				'message' => $validator->messages()
		   	], 422);
		}

		$path = storage_path(env('USUARIO_IMG_PATH'));
		File::isDirectory($path) or File::makeDirectory($path, 0775, true, true);

		$archivo_imagen = Image::make(file_get_contents($request->usu_img));
		$path_archivo = $path.$usuario->usu_id.'.'.config('extensions')[$archivo_imagen->mime];
		$archivo_imagen->save($path_archivo);

		$usuario->usu_imagen = $path_archivo;	
		$usuario->save();

		return $this->respond('Imagen de usuario actualizada');
	}

    /**
     * Retarna todos los puntos de evaluacion del mapa
     *
     * GET
     *
     * @param  Request $request
     * @return json 			Lista de todos los puntos de evaluacion
     */
    public function show_evaluation_points()
    {
        $usuario = JWTAuth::toUser();

        $puntos_evaluacion = $usuario->punto_evaluacion;

        $resource = new Collection($puntos_evaluacion, new PuntoEvaluacionTransformer());
        $fractal = new Manager();
        $data = $fractal->createData($resource)->toArray();

        return $this->respond('Puntos de evaluación', $data);
    }

}
