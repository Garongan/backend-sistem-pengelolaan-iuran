<?php

namespace App\Http\Controllers;

use App\Utils\CommonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        $validators = Validator::make(request(['email', 'password']), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8']
        ]);

        if ($validators->fails()) {
            return CommonResponse::commonResponse(401, 'Error', ['message' => $validators->errors()]);
        }

        $credentials = request(['email', 'password']);

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
