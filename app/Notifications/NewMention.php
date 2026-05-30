<?php
namespace App\Notifications;

use App\Models\{Post, User};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewMention extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $actor, public Post $post) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'actor'   => $this->actor->name,
            'message' => $this->actor->name . ' vous a mentionné dans une publication.',
            'post_id' => $this->post->id,
            'type'    => 'mention',
        ];
    }
}
