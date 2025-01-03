<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\CommonResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(8);
        return CommonResponse::commonResponse(
            200,
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
                404,
                'Error',
                ['message' => 'User not found']
            );
        }
        return CommonResponse::commonResponse(
            200,
            'Success',
            ['data' => $user]
        );
    }
}
