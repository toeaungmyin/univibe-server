<?php

use App\Http\Controllers\api\v1\auth\ForgetPasswordController;
use App\Http\Controllers\api\v1\auth\LoginController;
use App\Http\Controllers\api\v1\auth\LogoutController;
use App\Http\Controllers\api\v1\auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/sign-in', [LoginController::class, 'login']);
Route::post('/v1/sign-up', [RegisterController::class, 'store']);
Route::post('/v1/users/verify', [RegisterController::class, 'verifyEmail']);
Route::post('/v1/users/{user}/verify/re-send', [RegisterController::class, 'reSendVerifyEmail']);
Route::post('/v1/logout', [LogoutController::class, 'logout'])->middleware(['auth:api']);
Route::post('/v1/recovery-email/send', [ForgetPasswordController::class, 'sendRecoveryEmail']);
Route::post('/v1/recovery-email/verify', [ForgetPasswordController::class, 'verifyRecoveryEmail']);
Route::post('/v1/password/reset', [ForgetPasswordController::class, 'resetPassword'])->middleware(['auth:api']);
