<?php

namespace App\Http\Controllers\api\v1\admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        return response()->json(new UserResource(Auth::user()));
    }
}
