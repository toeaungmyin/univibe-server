<?php

namespace App\Http\Controllers\api\v1\admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\admin\UserResource;
use App\Models\BannedUser;
use App\Models\User;
use App\Models\WarningUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile()
    {
        return response()->json(new UserResource(Auth::user()));
    }

    public function index()
    {
        $users = User::role('user')->where('email_verified', true)->paginate(10);
        return response()->json(UserResource::collection($users));
    }

    public function show(User $user)
    {
        return response()->json(new UserResource($user));
    }

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

    public function ban(User $user, Request $request)
    {
        // Assuming you have an authenticated admin user who initiates the banning
        $admin = Auth::user();

        // Validate the reason title and description using Validator
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        // If validation fails, return a JSON response with error messages
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
        }

        // Ban the user using the BannedUser model method
        BannedUser::create([
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Optionally you can perform additional actions like sending notifications, updating user status, etc.

        // Return a JSON response with success message
        return response()->json([
            'user' => new UserResource($user),
            'message' => 'User has been banned successfully'
        ]);
    }

    public function unban(User $user)
    {
        $bannedUser = BannedUser::where('user_id', $user->id)->first();

        if (!$bannedUser) {
            return response()->json(['message' => 'User is not banned.'], 422); // Return a JSON response indicating the user is not banned.
        }

        $bannedUser->delete();


        return response()->json([
            'user' => new UserResource($user),
            'message' => 'User has been unbanned successfully.'
        ]);
    }

    public function warn(User $user, Request $request)
    {
        // Assuming you have an authenticated admin user who issues the warning
        $admin = Auth::user();

        // Validate the warning title and description using Validator
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        // If validation fails, return a JSON response with error messages
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
        }

        // Issue the warning using the WarningUser model method
        WarningUser::create([
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);

        // Optionally you can perform additional actions like sending notifications, updating user status, etc.

        // Return a JSON response with success message
        return response()->json([
            'user' => new UserResource($user),
            'message' => 'User has been warned successfully'
        ]);
    }

}
