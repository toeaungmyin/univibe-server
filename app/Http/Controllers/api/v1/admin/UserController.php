<?php

namespace App\Http\Controllers\api\v1\admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        return response()->json(new UserResource(Auth::user()));
    }

    public function index()
    {
        $users = User::where('email_verified', true)->role('user')->paginate(10);
        return response()->json(UserResource::collection($users));
    }
}
