<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowingController extends Controller
{
    public function store(User $user)
    {
        // Ensure the authenticated user is not trying to follow themselves
        $follower = Auth::user();

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
            'message' => 'You followed ' . $user->username
        ], 200);
    }


    

}
