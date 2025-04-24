<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class FollowController extends Controller
{
    public function follow(User $user): JsonResponse
    {
        if (Auth::id() === $user->id) {
            return response()->json(['message' => "You can't keep up with yourself"], 400);
        }

        Auth::user()->follow($user);

        return response()->json([
            'message' => 'Success follow',
            'following_count' => Auth::user()->following()->count(),
            'followers_count' => $user->followers()->count(),
        ]);
    }
    public function unfollow(User $user): JsonResponse
    {
        Auth::user()->unfollow($user);

        return response()->json([
            'message' => 'Unfollowed',
            'following_count' => Auth::user()->following()->count(),
            'followers_count' => $user->followers()->count(),
        ]);
    }
    public function followers(User $user): JsonResponse
    {
        return response()->json([
            'followers' => $user->followers()
                ->select('users.id', 'users.name', 'users.email')
                ->get(),
        ]);
    }
    public function following(User $user): JsonResponse
    {
        return response()->json([
            'following' => $user->following()
                ->select('users.id', 'users.name', 'users.email')
                ->get(),
        ]);
    }
}
