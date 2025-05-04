<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewLikeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $liker;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $liker)
    {
        $this->liker = $liker;
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
            'liker_id' => $this->liker->id,
            'liker_name' => $this->liker->name,
            'liker_picture' => $this->liker->profile_photo_url ?? null,
            'message' => $this->liker->name . 'liked your post.',
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
                'liker_id' => $this->liker->id,
                'liker_name' => $this->liker->name,
                'liker_picture' => $this->liker->profile_photo_url ?? null,
                'message' => $this->liker->name . 'liked your post.',
            ]
        ]);
    }
}
