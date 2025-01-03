<?php

namespace App\Http\Controllers;

use App\Utils\CommonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
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
            return CommonResponse::commonResponse(Response::HTTP_UNAUTHORIZED, 'Error', ['message' => 'Unauthorized']);
        }

        return $this->respondWithToken(Response::HTTP_OK, $token);
    }

    public function logout()
    {
        Auth::logout();
        return CommonResponse::commonResponse(Response::HTTP_OK, 'Success', ['message' => 'Logout successfull']);
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
