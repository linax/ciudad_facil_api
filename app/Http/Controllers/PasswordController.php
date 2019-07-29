<?php 

namespace App\Http\Controllers;

use App\Traits\ResetsPasswords;

class PasswordController extends ApiController 
{
	use ResetsPasswords;

    public function __construct()
    {
    	$this->broker = 'users';
    }

    public function restore($token)
    {
    	return view('auth.emails.password_reset', ['token' => $token]);
    }

}
