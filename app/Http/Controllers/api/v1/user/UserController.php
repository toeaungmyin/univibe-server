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

    public function update(UserUpdateRequest $request, User $user)
    {

        if ($user->id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validated();

        if ($request->hasFile('profile_url')) {
            $data['profile_url'] = $this->uploadProfilePhoto($request->file('profile_url'));
        }

        $user->update($data);

        return response()->json(new UserResource($user));
    }

    private function uploadProfilePhoto($photo)
    {
        $photoPath = $photo->store('uploads/profiles', 'public');
        return Storage::disk('public')->url($photoPath);
    }

    public function uploadImage(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust validation rules as needed
        ]);

        // Handle the image upload
        if ($request->hasFile('profile')) {

            return response()->json(['message' => 'Image uploaded successfully']);
        }

        return response()->json(['message' => 'Image upload failed'], 400);
    }

    public function reportUser(Request $request, User $user)
    {
    }

    public function suggestedUser()
    {
        // Get the authenticated user
        $user = Auth::user();

        $followerIds = $user->followers->pluck('id')->toArray();
        $followingIds = $user->followings->pluck('id')->toArray();

        // Get 5 random users who are neither followers nor following the authenticated user
        $randomUsers = User::role('user')->whereNotIn('id', array_merge($followerIds, $followingIds))
            ->where('id', '!=', $user->id)
            ->inRandomOrder()
            ->take(5)
            ->get();


        if (!$randomUsers) {
            return response()->json([
                'status' => false,
                'message' => 'No random user found to follow.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'random_users' => $randomUsers
        ], 200);
    }

    public function search(Request $request)
    {
        $query = $request->input('query'); // Get the search query from the request

        $users = User::where('username', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->select('id', 'username', 'profile_url')
            ->get();

        return response()->json(['users' => $users]);
    }


}
