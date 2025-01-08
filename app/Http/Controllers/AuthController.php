<?php

namespace App\Http\Controllers;

use App\Utils\CommonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController
{
    public function login()
    {
        $validated = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $credentials = request(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return CommonResponse::commonResponse(Response::HTTP_BAD_REQUEST, 'Error', ['message' => 'Wrong username and password']);
        }

        $user = Auth::user()->select('name')->get();

        return $this->respondWithToken(Response::HTTP_OK, $token, $user);
    }

    public function logout()
    {
        Auth::logout();
        return CommonResponse::commonResponse(Response::HTTP_OK, 'Success', ['message' => 'Logout successfull']);
    }

    protected function respondWithToken($statusCode, $token, $user)
    {
        return CommonResponse::commonResponse($statusCode, 'Success', [
            'data' => [
                'token' => $token,
                'user' => $user
            ]
        ]);
    }
}
