<?php

namespace App\Http\Controllers;

use App\Utils\CommonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function me()
    {
        //
        $user = Auth::user();
        if ($user == null) {
            return CommonResponse::commonResponse(404, 'Error', ['message' => 'User not found']);
        }
        return CommonResponse::commonResponse(200, 'Success', ['data' => $user]);
    }
}
