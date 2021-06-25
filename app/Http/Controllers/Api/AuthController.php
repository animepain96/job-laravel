<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!$token = $this->guard()->attempt($request->only('email', 'password'))) {
            return response()
                ->json(['status' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        $this->guard()->logout();
        return response()
            ->json(['status' => 'success']);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    public function me()
    {
        return response()
            ->json(['data' => auth()->user(), 'status' => 'success']);
    }

    protected function respondWithToken($token)
    {
        return response()
            ->json(
                [
                    'data' => [
                        'access_token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => $this->guard()->factory()->getTTL() * 60,
                        'user' => $this->guard()->user(),
                    ],
                    'status' => 'success',
                ]);
    }

    public function guard()
    {
        return Auth::guard('api');
    }
}
