<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class LikeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $content;

    public function __construct(User $user, $content)
    {
        $this->user = $user;
        $this->content = $content;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'content_id' => $this->content['id'],
            'content_type' => $this->content['type'],
            'message' => "liked your " . $this->content['type'],
            'type' => 'like'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
