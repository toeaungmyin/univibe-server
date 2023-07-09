<?php

namespace App\Http\Controllers\api\v1\user\auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => ['Logout successful']
        ], 200);
    }
}
