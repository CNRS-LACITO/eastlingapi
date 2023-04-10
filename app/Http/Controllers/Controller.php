<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
//import auth facades
  use Illuminate\Support\Facades\Auth;
  
class Controller extends BaseController
{
    //
      //Add this method to the Controller class
  protected function respondWithToken($token)
    {
        $user = Auth::user();
        $user['last_login_date'] = date('y-m-d H:i:s');
        $user->save();

        Auth::factory()->setTTL(1440);

        return response()->json([
            'username' => $user->username,
            'email' => $user->email,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 1440,
        ], 200);
    }

    public function phpinfo(){
      return phpinfo();
    }
}
