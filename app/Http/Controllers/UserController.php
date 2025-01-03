<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\CommonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UserController
{
    public function index()
    {
        $users = User::paginate(8);
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $users]
        );
    }

    public function me()
    {
        //
        $user = Auth::user();
        if ($user == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['message' => 'User not found']
            );
        }
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $user]
        );
    }
}
