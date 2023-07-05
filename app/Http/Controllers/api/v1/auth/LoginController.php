<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if ($user->email_verified === 1) {
                    $token = $user->createToken($user->email . '_' . now())->accessToken;
                    return response()->json([
                        'token' =>  $token,
                        'user' => $user,
                        'message' => ['Login successful']
                    ], 200);
                } else {
                    $response = ["message" => ["You need to vertify your email address"]];
                    return response()->json($response, 422);
                }
            } else {
                $response = ["message" => ["Invalid email or password"]];
                return response()->json($response, 422);
            }
        } else {
            $response = ["message" => ['Invalid email or password']];
            return response()->json($response, 422);
        }
    }
}
