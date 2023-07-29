<?php

namespace App\Http\Controllers\api\v1\admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\admin\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    // public function update(UserUpdateRequest $request, User $user)
    // {

    //     $user->update($request->only([
    //         'username',
    //         'email',
    //         'birthday',
    //         'online'
    //     ]));

    //     if ($request->has('profile_url')) {
    //         $photo = $request->file('profile_url');
    //         $photoName = time() . '.' . $photo->getClientOriginalExtension();
    //         $photo_path = 'uploads/profile';
    //         $photo->storeAs($photo_path, $photoName);
    //         $photo_url = $photo_path . '/' . $photoName;
    //         $user->profile_url = $photo_url;
    //         $user->save();
    //     }

    //     return response()->json(new UserResource($user));
    // }

    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->validated();

        if ($request->hasFile('profile_url')) {
            $data['profile_url'] = $this->uploadProfilePhoto($request->file('profile_url'));
        }

        $user->update($data);

        return response()->json(new UserResource($user));
    }

    private function uploadProfilePhoto($photo)
    {
        $photoPath = $photo->store('uploads/profile', 'public');
        return Storage::disk('public')->url($photoPath);
    }

}
