<?php

namespace App\Http\Controllers\api\v1\admin\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout()
    {
        User::find(Auth::user())->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => ['Logout successful']
        ], 200);
    }
}
