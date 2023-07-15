<?php

namespace App\Http\Controllers\api\v1\user\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Verification_Code;
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
            'birthday' => $request->birthday,
        ])->assignRole('user');

        $user->sendVertifyEmail();

        return response()->json([
            'status' => true,
            'data' => [
                'user_id' => $user->id,
                'code_expire_time' => $this->CODE_EXPIRE_TIME
            ],
            'message' => 'Vertify email was sent'
        ]);
    }

    public function reSendVerifyEmail(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user not found'
            ]);
        };

        $user->sendVertifyEmail();

        return response()->json([
            'status' => true,
            'data' => [
                'code_expire_time' => $this->CODE_EXPIRE_TIME
            ],
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
        $code = Verification_Code::where('code', $request->code)->first();
        $user = User::find($code->user_id);

        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Login attempt'
            ], 401);
        }

        $creationTime = Carbon::parse($code->created_at);
        $currentTime = Carbon::now();

        if ($currentTime->diffInSeconds($creationTime) < $this->CODE_EXPIRE_TIME) {
            $user->update([
                'email_verified' => 1,
                'email_verified_at' => $currentTime,
            ]);
            $user->assignRole('user');
            $code->delete();
            $token = $user->createToken($user->email . '_' . now())->accessToken;

            return response()->json([
                'status' => true,
                'token' =>  $token,
                'message' => 'Your email is verified successfully'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Verification code expired'
        ], 401);
    }
}
