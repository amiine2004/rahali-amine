<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PostController as AdminPostController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Posts routes
    Route::apiResource('posts', PostController::class);
    Route::get('/users/{userId}/posts', [PostController::class, 'userPosts']);
    
    // Comments routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::get('/comments/{comment}/replies', [CommentController::class, 'replies']);
    
    // Likes routes
    Route::post('/posts/{post}/like', [LikeController::class, 'togglePostLike']);
    Route::post('/comments/{comment}/like', [LikeController::class, 'toggleCommentLike']);
    
    // Friendship routes
    Route::post('/users/{user}/friend-request', [FriendshipController::class, 'sendFriendRequest']);
    Route::post('/friendships/{friendship}/accept', [FriendshipController::class, 'acceptFriendRequest']);
    Route::post('/friendships/{friendship}/decline', [FriendshipController::class, 'declineFriendRequest']);
    Route::delete('/users/{user}/friend', [FriendshipController::class, 'removeFriend']);
    Route::get('/friend-requests', [FriendshipController::class, 'pendingRequests']);
    Route::get('/friends', [FriendshipController::class, 'friends']);
    
    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/dashboard/activity', [DashboardController::class, 'recentActivity']);
        
        // User management
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::put('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
        Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus']);
        
        // Post management
        Route::get('/posts', [AdminPostController::class, 'index']);
        Route::get('/posts/{post}', [AdminPostController::class, 'show']);
        Route::post('/posts/{post}/toggle-status', [AdminPostController::class, 'toggleStatus']);
        Route::delete('/posts/{post}', [AdminPostController::class, 'destroy']);
    });
});