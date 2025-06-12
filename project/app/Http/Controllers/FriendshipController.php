<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FriendshipController extends Controller
{
    public function sendFriendRequest(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot send friend request to yourself'
            ], 400);
        }

        $existingFriendship = Friendship::where(function ($query) use ($currentUser, $user) {
            $query->where('user_id', $currentUser->id)->where('friend_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('user_id', $user->id)->where('friend_id', $currentUser->id);
        })->first();

        if ($existingFriendship) {
            return response()->json([
                'success' => false,
                'message' => 'Friend request already exists'
            ], 400);
        }

        Friendship::create([
            'user_id' => $currentUser->id,
            'friend_id' => $user->id,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Friend request sent successfully'
        ]);
    }

    public function acceptFriendRequest(Request $request, Friendship $friendship): JsonResponse
    {
        if ($friendship->friend_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $friendship->update(['status' => 'accepted']);

        return response()->json([
            'success' => true,
            'message' => 'Friend request accepted'
        ]);
    }

    public function declineFriendRequest(Request $request, Friendship $friendship): JsonResponse
    {
        if ($friendship->friend_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $friendship->update(['status' => 'declined']);

        return response()->json([
            'success' => true,
            'message' => 'Friend request declined'
        ]);
    }

    public function removeFriend(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        $friendship = Friendship::where(function ($query) use ($currentUser, $user) {
            $query->where('user_id', $currentUser->id)->where('friend_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('user_id', $user->id)->where('friend_id', $currentUser->id);
        })->where('status', 'accepted')->first();

        if (!$friendship) {
            return response()->json([
                'success' => false,
                'message' => 'Friendship not found'
            ], 404);
        }

        $friendship->delete();

        return response()->json([
            'success' => true,
            'message' => 'Friend removed successfully'
        ]);
    }

    public function pendingRequests(Request $request): JsonResponse
    {
        $pendingRequests = Friendship::with('user')
            ->where('friend_id', $request->user()->id)
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pendingRequests
        ]);
    }

    public function friends(Request $request): JsonResponse
    {
        $friends = $request->user()->friends()->get();

        return response()->json([
            'success' => true,
            'data' => $friends
        ]);
    }
}