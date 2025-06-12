<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_posts' => Post::count(),
            'active_posts' => Post::where('is_active', true)->count(),
            'total_comments' => Comment::count(),
            'active_comments' => Comment::where('is_active', true)->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_posts_today' => Post::whereDate('created_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function recentActivity(): JsonResponse
    {
        $recentUsers = User::latest()->take(5)->get();
        $recentPosts = Post::with('user')->latest()->take(5)->get();
        $recentComments = Comment::with(['user', 'post'])->latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'recent_users' => $recentUsers,
                'recent_posts' => $recentPosts,
                'recent_comments' => $recentComments,
            ]
        ]);
    }
}