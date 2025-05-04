<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $commenter;
    public string $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $commenter, string $comment)
    {
        $this->commenter = $commenter;
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'commenter_id' => $this->commenter->id,
            'commenter_name' => $this->commenter->name,
            'commenter_picture' => $this->commenter->profile_photo_url ?? null,
            'comment' => $this->comment,
            'message' => $this->commenter->name . ' Commented "' . $this->comment . '"',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'read_at' => null,
            'created_at' => now()->toIso8601String(),
            'data' => [
                'commenter_id' => $this->commenter->id,
                'commenter_name' => $this->commenter->name,
                'commenter_picture' => $this->commenter->profile_photo_url ?? null,
                'comment' => $this->comment,
                'message' => $this->commenter->name . ' commented "' . $this->comment . '"',
            ]
        ]);
    }
}
