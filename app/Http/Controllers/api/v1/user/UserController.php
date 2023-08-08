<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\user\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function show(User $user)
    {
        return response()->json(new UserResource($user));
    }

    public function update(UserUpdateRequest $request)
    {
        $authenticatedUser = Auth::user();
        $data = $request->validated();

        if ($request->hasFile('profile_url')) {
            $data['profile_url'] = $this->uploadProfilePhoto($request->file('profile_url'));
        }

        $authenticatedUser->update($data);

        return response()->json(new UserResource($authenticatedUser));
    }

    private function uploadProfilePhoto($photo)
    {
        $photoPath = $photo->store('uploads/profiles', 'public');
        return Storage::disk('public')->url($photoPath);
    }


}
