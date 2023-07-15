<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use App\Models\Following;
use App\Models\User;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function follow(Request $request)
    {
        if (!$request->following_id) {
            return response()->json([
                'status' => true,
                'message' => 'following_id is required'
            ]);
        }

        Following::create([
            'follower_id' => $request->user()->id,
            'following_id' => $request->following_id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'You followed ' . User::find($request->following_id)->username
        ]);
    }
}
