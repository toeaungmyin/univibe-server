<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\admin\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowingController extends Controller
{
    public function store(User $user)
    {
        // Ensure the authenticated user is not trying to follow themselves
        $follower = User::find(Auth::user()->id);

        if ($follower->id === $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot follow yourself.'
            ], 400);
        }

        // Check if the user is already being followed to avoid duplication
        if ($follower->followings()->where('following_id', $user->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'You are already following ' . $user->username
            ], 400);
        }

        // Create the relationship
        $follower->followings()->attach($user);

        return response()->json([
            'status' => true,
            'auth' => new UserResource($follower),
            'message' => 'You followed ' . $user->username
        ], 200);
    }

    public function unFollow(User $user)
    {
        $follower = User::find(Auth::user()->id);

        if ($follower->id === $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot unfollow yourself.'
            ], 400);
        }

        // Check if the user is currently following the user to unfollow
        if (!$follower->followings()->where('following_id', $user->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'You are not following ' . $user->username
            ], 400);
        }

        $follower->followings()->detach($user);

        return response()->json([
            'status' => true,
            'auth' => new UserResource($follower),
            'message' => 'You unfollowed ' . $user->username
        ], 200);
    }





}
