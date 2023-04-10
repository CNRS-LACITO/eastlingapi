<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

//import auth facades
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    /**
     * Update the document for the given ID.
     *
     * @param  string  $word
     * @return Response
     */
    public function hash($word){
        return response()->json(app('hash')->make($word), 201);
    }
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        
        //validate incoming request 
        $this->validate($request, [
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        try {
           
            $user = new User;
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->organization = $request->input('organization');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            //return response()->json(['message' => 'User Registration Failed!'], 409);
            return response()->json(['message' => $e], 409);

        }

    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['username', 'password']);

        if (! $token = Auth::attempt($credentials,true)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        return $this->respondWithToken($token);
    }



    
}