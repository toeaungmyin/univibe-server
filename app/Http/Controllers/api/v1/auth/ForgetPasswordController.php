<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\ForgetPasswordRequest;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordController extends Controller
{
    private $CODE_EXPIRE_TIME = 120;
    public function sendRecoveryEmail(ForgetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $response = ["message" => ['Invalid email']];
            return response()->json($response, 422);
        } else {
            PasswordReset::where('email', $user->email)->first()->delete();
            $response = $user->sendPasswordResetEmail();
        }

        return response()->json($response);
    }

    public function verifyRecoveryEmail(Request $request)
    {
        if ($request->code === null || $request->code === '') {
            return response()->json([
                'status' => false,
                'message' => 'Invalid vertification code'
            ], 401);
        }

        $password_reset = PasswordReset::where('code', $request->code)->first();

        if ($password_reset == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid vertification code'
            ], 401);
        }

        $creationTime = Carbon::parse($password_reset->created_at);
        $currentTime = Carbon::now();

        if ($currentTime->diffInSeconds($creationTime) < $this->CODE_EXPIRE_TIME) {
            $password_reset->update([
                'code_verified' => true,
                'code' => ''
            ]);

            $user = User::where('email', $password_reset->email)->first();

            if ($user) {
                $token = $user->createToken($user->email . '_' . now())->accessToken;

                return response()->json([
                    'status' => true,
                    'token' =>  $token,
                    'data' => $user,
                    'message' => 'Your recovery email is verified successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Verification code expired'
        ], 401);
    }

    public function resetPassword(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => ['Password reset successful']
        ], 200);
    }
}
