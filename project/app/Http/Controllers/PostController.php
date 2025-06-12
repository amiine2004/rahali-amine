<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])
            ->where('is_active', true)
            ->where(function ($query) use ($request) {
                $query->where('privacy', 'public')
                    ->orWhere(function ($q) use ($request) {
                        $q->where('privacy', 'friends')
                          ->whereHas('user.friends', function ($friendQuery) use ($request) {
                              $friendQuery->where('friend_id', $request->user()->id);
                          });
                    })
                    ->orWhere('user_id', $request->user()->id);
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'privacy' => 'in:public,friends,private'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'image' => $imagePath,
            'privacy' => $request->privacy ?? 'public',
        ]);

        $post->load(['user', 'comments', 'likes']);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post
        ], 201);
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['user', 'comments.user', 'likes']);

        return response()->json([
            'success' => true,
            'data' => $post
        ]);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'privacy' => 'in:public,friends,private'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $post->update([
            'content' => $request->content,
            'privacy' => $request->privacy ?? $post->privacy,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }

    public function userPosts(Request $request, $userId): JsonResponse
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }
}