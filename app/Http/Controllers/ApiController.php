<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuario;
use Hash;
use JWTAuth;

class APIController extends Controller
{
    protected $statusCode = 200; //HTTP::OK

    public function register(Request $request)
    {        
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        User::create($input);
        return response()->json(['result'=>true]);
    }
    
    public function login(Request $request)
    {
        $this->validate($request, [
                'usu_eml'    => 'required|email|max:255',
                'usu_pss' => 'required',
            ]);

        if (!$token = JWTAuth::attempt([
                Usuario::$field_mask['usu_eml'] => $request->input('usu_eml'), 
                Usuario::$field_mask['usu_pss'] => $request->input('usu_pss')
            ])
        ) {
            return response()->json([
                'message' => 'Error en usuario o password',
                'token' => null
                ], 200);
        }

        return response()->json([
            'message' => 'Login satisfactorio',
            'token' => $token
            ]);
    }
    
    public function get_user_details(Request $request)
    {
        $input = $request->all();
        $user = JWTAuth::toUser($input['token']);
        return response()->json(['result' => $user]);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function respond($message, $data = [])
    {
        $headers = array (
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        (!empty($message)) ? $data['message'] = $message : $data['message'] = 'Valores de respuesta';

        return response()->json($data, $this->getStatusCode(), $headers);
    }
    
}