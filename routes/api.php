<?php

use App\Http\Controllers\api\v1\auth\LoginController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [LoginController::class, 'login']);
Route::post('/sign-up', [RegisterController::class, 'store']);
Route::post('/users/verify', [RegisterController::class, 'vertifyEmail']);
Route::post('/users/{user}/verify/re-send', [RegisterController::class, 'reSendVertifyEmail']);
// Route::post('/logout', [Logout::class, 'logout']);
