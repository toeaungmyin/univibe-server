<?php

use App\Http\Controllers\api\v1\admin\auth\LoginController as AdminLoginController;
use App\Http\Controllers\api\v1\admin\auth\LogoutController as AdminLogoutController;
use App\Http\Controllers\api\v1\admin\auth\ForgetPasswordController as AdminForgetPasswordController;
use App\Http\Controllers\api\v1\admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\api\v1\admin\PostController as AdminPostController;
use App\Http\Controllers\api\v1\admin\ReportController as AdminReportController;
use App\Http\Controllers\api\v1\admin\UserController as AdminUserController;

use App\Http\Controllers\api\v1\user\auth\RegisterController;
use App\Http\Controllers\api\v1\user\auth\LoginController;
use App\Http\Controllers\api\v1\user\auth\LogoutController;
use App\Http\Controllers\api\v1\user\auth\ForgetPasswordController;
use App\Http\Controllers\api\v1\user\CommentController;
use App\Http\Controllers\api\v1\user\FollowingController;
use App\Http\Controllers\api\v1\user\MessageController;
use App\Http\Controllers\api\v1\user\NotificationController;
use App\Http\Controllers\api\v1\user\PostController;
use App\Http\Controllers\api\v1\user\UserController;

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

// user Authentication
Route::post('/v1/sign-in', [LoginController::class, 'login']);
Route::post('/v1/sign-up', [RegisterController::class, 'store']);
Route::post('/v1/users/verify', [RegisterController::class, 'verifyEmail']);
Route::post('/v1/users/verify/re-send', [RegisterController::class, 'reSendVerifyEmail']);
Route::post('/v1/logout', [LogoutController::class, 'logout'])->middleware(['auth:api']);
Route::post('/v1/recovery-email/send', [ForgetPasswordController::class, 'sendRecoveryEmail']);
Route::post('/v1/recovery-email/verify', [ForgetPasswordController::class, 'verifyRecoveryEmail']);
Route::post('/v1/password/reset', [ForgetPasswordController::class, 'resetPassword'])->middleware(['auth:api']);

// admin Authentication
Route::post('/v1/admin/sign-in', [AdminLoginController::class, 'login']);
Route::post('/v1/admin/logout', [AdminLogoutController::class, 'logout'])->middleware(['auth:api', 'role:admin']);
Route::post('/v1/admin/recovery-email/send', [AdminForgetPasswordController::class, 'sendRecoveryEmail']);
Route::post('/v1/admin/recovery-email/verify', [AdminForgetPasswordController::class, 'verifyRecoveryEmail']);
Route::post('/v1/admin/password/reset', [AdminForgetPasswordController::class, 'resetPassword'])->middleware(['auth:api', 'role:admin']);

// user routes
Route::middleware(['auth:api'])->prefix('/v1')->group(function () {
    Route::get('/me', [UserController::class, 'profile']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'delete']);
    Route::get('/search-user', [UserController::class, 'search']);

    Route::post('/users/{user}/report', [UserController::class, 'report']);

    Route::get('/users/suggest/random', [UserController::class, 'suggestedUser']);
    Route::get('/users/{user}/posts', [PostController::class, 'getUserPosts']);


    Route::post('/users/{user}/follow', [FollowingController::class, 'store']);
    Route::post('/users/{user}/unfollow', [FollowingController::class, 'unfollow']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'delete']);

    Route::post('/posts/{post}/react', [PostController::class, 'reactToPost']);
    Route::post('/posts/{post}/report', [PostController::class, 'report']);


    Route::post('/comments', [CommentController::class, 'store']);
    // Update a comment
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    // Delete a comment
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);


    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notificationId}/mark-as-read', [NotificationController::class, 'markAsRead']);

    Route::get('/conversations', [MessageController::class, 'index']);
    Route::get('/users/{user}/conversation', [MessageController::class, 'listMessages']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

});

// admin routes
Route::middleware(['auth:api', 'role:admin'])->prefix('/v1/admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index']);
    Route::get('/users-by-date', [AdminDashboardController::class, 'getUsersByDate']);
    Route::get('/posts-by-date', [AdminDashboardController::class, 'getPostsByDate']);

    Route::get('/me', [AdminUserController::class, 'profile']);

    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{user}', [AdminUserController::class, 'show']);
    Route::put('/users/{user}', [AdminUserController::class, 'update']);
    Route::delete('/users/{user}', [AdminUserController::class, 'delete']);

    Route::post('/users/{user}/ban', [AdminUserController::class, 'ban']);
    Route::post('/users/{user}/unban', [AdminUserController::class, 'unban']);

    Route::post('/users/{user}/warn', [AdminUserController::class, 'warn']);
    Route::delete('/users/{user}/warnings/{warningId}', [AdminUserController::class, 'deleteWarning']);

    Route::get('/search-user', [AdminUserController::class, 'search']);


    Route::get('/posts', [AdminPostController::class, 'index']);
    Route::get('/posts/{post}', [AdminPostController::class, 'show']);
    Route::delete('/posts/{post}', [AdminPostController::class, 'delete']);

    Route::get('/search-post', [AdminPostController::class, 'search']);


    Route::get('/users-reports', [AdminReportController::class, 'getUsersReports']);
    Route::get('/search-user-report', [AdminReportController::class, 'searchUserReport']);

    Route::get('/posts-reports', [AdminReportController::class, 'getPostsReports']);

    Route::get('/search-post-report', [AdminReportController::class, 'searchPostReport']);

    Route::delete('/users-reports/{report}', [AdminReportController::class, 'deleteUserReport']);
    Route::delete('/posts-reports/{report}', [AdminReportController::class, 'deletePostReport']);

});
