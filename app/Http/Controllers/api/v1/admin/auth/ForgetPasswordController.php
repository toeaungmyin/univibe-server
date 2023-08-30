<?php

namespace App\Http\Controllers\api\v1\admin\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\ForgetPasswordRequest;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            PasswordReset::where('user_id', $user->id)->delete();
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

            $user = User::find($password_reset->user_id);

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
        $validator = Validator::make($request->all(), [
            'password' => [
                'nullable',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'
                // requires at least one lowercase letter
                // requires at least one uppercase letter
                // requires at least one special character from the specified symbols
                // matches a combination of letters, digits, and special characters with a minimum length of 8 characters.
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ], 422);
        }

        $validData = $validator->validated();

        $user = User::find(Auth::user()->id);
        $password_reset = PasswordReset::where('user_id', $user->id)->first();
        if ($password_reset && $password_reset->code_verified) {

            $user->update([
                'password' => Hash::make($validData['password'])
            ]);

            $password_reset->delete();

            return response()->json([
                'status' => true,
                'message' => ['Password reset successful']
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => ['Password reset is not allowed for this user']
        ], 422);
    }
}
