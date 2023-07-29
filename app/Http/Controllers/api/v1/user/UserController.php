<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\user\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function profile()
    {
        return response()->json(new UserResource(Auth::user()));
    }

    public function index()
    {
        $users = User::where('email_verified', true)->role('user')->get();
        return response()->json(UserResource::collection($users));
    }

    // public function update(User $user, UserUpdateRequest $request)
    // {

    //     // $user->update($request->filled([
    //     //     'username',
    //     //     'email',
    //     //     'birthday',
    //     //     'profile_url',
    //     //     'online'
    //     // ]));
    //     return $request;
    // }


}
