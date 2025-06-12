<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Post::with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('content', 'like', "%{$search}%");
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->has('privacy')) {
            $query->where('privacy', $request->privacy);
        }

        $posts = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['user', 'comments.user', 'likes']);

        return response()->json([
            'success' => true,
            'data' => $post
        ]);
    }

    public function toggleStatus(Post $post): JsonResponse
    {
        $post->update(['is_active' => !$post->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Post status updated successfully',
            'data' => $post
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }
}