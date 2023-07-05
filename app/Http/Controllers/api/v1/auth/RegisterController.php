<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    private $CODE_EXPIRE_TIME = 120;
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birthday' => Carbon::parse($request->birthday),
        ]);

        $user->sendVertifyEmail();

        return response()->json([
            'status' => true,
            'data' => ['user_id' => $user->id],
            'message' => 'Vertify email was sent'
        ]);
    }

    public function reSendVerifyEmail(User $user)
    {
        $user->sendVertifyEmail();

        return response()->json([
            'status' => true,
            'message' => 'Vertify email was sent'
        ]);
    }

    public function verifyEmail(Request $request)
    {
        if ($request->code === null || $request->code === '') {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login attempt'
            ], 401);
        }

        $user = User::where('email_verification_code', $request->code)->first();

        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Login attempt'
            ], 401);
        }

        $creationTime = Carbon::parse($user->updated_at);
        $currentTime = Carbon::now();

        if ($currentTime->diffInSeconds($creationTime) < $this->CODE_EXPIRE_TIME) {
            $user->update([
                'email_verified' => 1,
                'email_verified_at' => $currentTime,
                'email_verification_code' => ''
            ]);

            $token = $user->createToken($user->email . '_' . now())->accessToken;

            return response()->json([
                'status' => true,
                'token' =>  $token,
                'user' => $user,
                'message' => 'Your email is verified successfully'
            ], 200);
        }

        $user->update([
            'email_verification_code' => ''
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Verification code expired'
        ], 401);
    }
}
