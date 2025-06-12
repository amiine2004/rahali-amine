<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    public function togglePostLike(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $post->decrementLikesCount();
            $message = 'Post unliked successfully';
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $post->incrementLikesCount();
            $message = 'Post liked successfully';
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'liked' => $liked,
                'likes_count' => $post->fresh()->likes_count
            ]
        ]);
    }

    public function toggleCommentLike(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();
        $like = $comment->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $message = 'Comment unliked successfully';
            $liked = false;
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
            $message = 'Comment liked successfully';
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'liked' => $liked,
                'likes_count' => $comment->likes()->count()
            ]
        ]);
    }
}