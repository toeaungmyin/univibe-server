<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;

class FollowingController extends Controller
{


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'following_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $follower = $request->user();
        $followingId = $request->input('following_id');
        $followingUser = User::find($followingId);

        if (!$followingUser) {
            return response()->json([
                'status' => false,
                'message' => 'User with ID ' . $followingId . ' not found'
            ], 404);
        }

        $follower->followings()->attach($followingId);

        return response()->json([
            'status' => true,
            'message' => 'You followed ' . $followingUser->username
        ], 200);
    }


}
