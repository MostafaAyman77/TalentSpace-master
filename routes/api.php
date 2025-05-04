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
use App\Http\Controllers\Api\VideoInteractionController;
use App\Models\FileMedia;
use App\Models\Comment;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\AdminOfferController;
use App\Models\Offer; // If using explicit binding

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

// ********************** Follow Notification ***********************
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
});


// ********************** comments and likes Notification ***********************

// ... other routes

// Define explicit route model binding if not done globally (optional but good practice)
// Route::model('fileMedia', FileMedia::class);
// Route::model('comment', Comment::class);

Route::prefix('videos/{fileMedia}')->middleware('auth:sanctum')->group(function () {
    // Comments Routes
    Route::get('/comments', [VideoInteractionController::class, 'getComments'])->name('videos.comments.index');
    Route::post('/add-comment', [VideoInteractionController::class, 'addComment'])->name('videos.comments.store');

    // Likes Routes
    Route::post('/toggle-like', [VideoInteractionController::class, 'toggleLike'])->name('videos.likes.toggle');
    Route::get('/likers', [VideoInteractionController::class, 'getLikers'])->name('videos.likers.index');
});

// Separate route for deleting a comment
Route::delete('/comments/{comment}', [VideoInteractionController::class, 'deleteComment'])
    ->middleware('auth:sanctum')
    ->name('videos.comments.destroy');
// *****************************



// --- Offer Routes (Investor & Talent) ---
Route::middleware(["auth:sanctum"])->group(function () {
    Route::post("/offers", [OfferController::class, "store"])->name("offers.store");
    Route::get("/offers/sent", [OfferController::class, "indexInvestor"])->name("offers.index.investor");
    Route::get("/offers/received", [OfferController::class, "indexTalent"])->name("offers.index.talent");
    Route::post("/offers/{offer}/respond", [OfferController::class, "respond"])->name("offers.respond");
});

// --- Admin Offer Routes ---
Route::middleware(["auth:sanctum"]) // <-- إزالة middleware الدور من هنا
    ->prefix("admin")->name("admin.")->group(function () {
    Route::get("/offers/pending", [AdminOfferController::class, "indexPending"])->name("offers.index.pending");
    Route::post("/offers/{offer}/decide", [AdminOfferController::class, "decide"])->name("offers.decide");
});


// Optional: Define explicit route model binding in AppServiceProvider or RouteServiceProvider
// Route::model("offer", Offer::class);
