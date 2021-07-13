<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\PasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->only('username', 'password');
        $data['active'] = true;

        if (!$token = $this->guard()->attempt($data)) {
            return response()
                ->json(['status' => 'error']);
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

    public function user()
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

    public function changePassword(PasswordRequest $request)
    {
        $password = $request->get('password');
        $user = $this->guard()->user();
        if($user->update(['password' => Hash::make($password)]))
        {
            $this->refresh();
            return response()
                ->json(['status' => 'success']);
        }

        return response()
            ->json(['status' => 'error'], 500);
    }
}
