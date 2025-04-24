<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class CommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $content;
    protected $comment;

    public function __construct(User $user, $content, $comment)
    {
        $this->user = $user;
        $this->content = $content;
        $this->comment = $comment;
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
            'comment' => $this->comment,
            'message' => "commented on your " . $this->content['type'],
            'type' => 'comment'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
