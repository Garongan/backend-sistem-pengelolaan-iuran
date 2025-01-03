<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Utils\CommonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (! $token = Auth::attempt($credentials)) {
            return CommonResponse::commonResponse(401, 'Error', ['message' => 'Unauthorized']);
        }

        return $this->respondWithToken(200, $token);
    }

    public function logout()
    {
        Auth::logout();
        return CommonResponse::commonResponse(200, 'Success', ['message' => 'Logout successfull']);
    }

    protected function respondWithToken($statusCode, $token)
    {
        return CommonResponse::commonResponse($statusCode, 'Success', [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
