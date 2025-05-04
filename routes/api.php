<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\FileMediaController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\SocialiteController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\API\FollowController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\NotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//----------------Auth----------------
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    //----------------Users----------------
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::apiResource('users', AdminController::class);


    //----------------Videos----------------
    Route::get('/videos', [FileMediaController::class, 'index']);
    Route::post('/video/upload', [FileMediaController::class, 'store']);
    Route::get('/video/show/{id}', [FileMediaController::class, 'show']);
    Route::delete('/video/delete/{id}', [FileMediaController::class, 'destroy']);

    // ----------------Followers----------------
    Route::post('/follow/{user}', [FollowController::class, 'follow']);
    Route::post('/unfollow/{user}', [FollowController::class, 'unfollow']);
    Route::get('/followers/{user}', [FollowController::class, 'followers']);
    Route::get('/following/{user}', [FollowController::class, 'following']);
});



//------------------Socialite----------------
Route::middleware(['api', 'web'])->group(function () {


    Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);


    Route::get('auth/facebook', [SocialiteController::class, 'redirectToFacebook']);
    Route::get('auth/facebook/callback', [SocialiteController::class, 'handleFacebookCallback']);

});

// ------------------OTP-----------------------

Route::prefix('auth')->group(function () {
    Route::post('/password/otp', [PasswordResetController::class, 'sendOtp']);
    Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
});

// **********************
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
});




//
use App\Models\User;
use App\Notifications\NewFollowerNotification;

Route::post('/test-notification', function () {
    $follower = User::query()->find(1);
    $target = User::query()->find(2);

    if ($follower && $target) {
        $target->notify(new NewFollowerNotification($follower));
        return response()->json(['message' => 'Notification sent']);
    }

    return response()->json(['error' => 'Users not found'], 404);
});

// like test
use App\Notifications\NewLikeNotification;

Route::post('/test-like-notification', function () {
    $liker = User::query()->find(1);
    $target = User::query()->find(2);

    if ($liker && $target) {
        $target->notify(new NewLikeNotification($liker));
        return response()->json(['message' => 'Like notification sent']);
    }

    return response()->json(['error' => 'Users not found'], 404);
});


// comment test
use App\Notifications\NewCommentNotification;

Route::post('/test-comment-notification', function () {
    $commenter = User::query()->find(1);
    $target = User::query()->find(2);
    $commentText = 'Test comment';

    if ($commenter && $target) {
        $target->notify(new NewCommentNotification($commenter, $commentText));
        return response()->json(['message' => 'Comment notification sent']);
    }

    return response()->json(['error' => 'Users not found'], 404);
});
