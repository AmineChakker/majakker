<?php
namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewComment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'actor'   => $this->comment->user->name,
            'message' => $this->comment->user->name . ' a commenté votre publication.',
            'post_id' => $this->comment->post_id,
            'type'    => 'comment',
        ];
    }
}
