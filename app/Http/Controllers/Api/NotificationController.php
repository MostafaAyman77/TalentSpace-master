<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\LikeNotification;
use App\Notifications\CommentNotification;
use App\Notifications\FollowNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Get all notifications for the authenticated user
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'notifications' => $notifications,
                'unread_count' => Auth::user()->unreadNotifications->count()
            ]
        ]);
    }

    // Mark a specific notification as read
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read'
        ]);
    }

    // Mark all notifications as read
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read'
        ]);
    }

    // Send a like notification
    public function sendLikeNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'content_id' => 'required',
            'content_type' => 'required|in:post,video,achievement'
        ]);

        $user = User::findOrFail($request->user_id);
        /** @var User $authUser */
        $authUser = Auth::user();

        $content = [
            'id' => $request->content_id,
            'type' => $request->content_type
        ];

        $user->notify(new LikeNotification($authUser, $content));

        return response()->json([
            'status' => 'success',
            'message' => 'Like notification sent'
        ]);
    }

    // Send a comment notification
    public function sendCommentNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'content_id' => 'required',
            'content_type' => 'required|in:post,video,achievement',
            'comment' => 'required|string'
        ]);

        $user = User::findOrFail($request->user_id);
        /** @var User $authUser */
        $authUser = Auth::user();

        $content = [
            'id' => $request->content_id,
            'type' => $request->content_type
        ];

        $user->notify(new CommentNotification($authUser, $content, $request->comment));

        return response()->json([
            'status' => 'success',
            'message' => 'Comment notification sent'
        ]);
    }

    // Send a follow notification
    public function sendFollowNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->notify(new FollowNotification(Auth::user()));

        return response()->json([
            'status' => 'success',
            'message' => 'Follow notification sent'
        ]);
    }
}
