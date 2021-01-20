<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login()
    {
        if (! $token = auth('api')->attempt(['phone' => request('phone'), 'password' => request('password') ,'is_deleted' => 0])) {
            return response()->json(['error' => 'Check your input'], 401);
        }

        return $this->respondWithToken($token);
    }

    
    
    public function me()
    {
        return response()->json(auth('api')->user());
    }

   
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

   
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    
    protected function respondWithToken($token)
    {
        $type=auth('api')->user()->type;
        // log::info(auth('api')->user()->$type);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => [auth('api')->user() ,auth('api')->user()->$type ],
            'expires_in'   => auth('api')->factory()->getTTL() * 60

        ]);
    }
}
