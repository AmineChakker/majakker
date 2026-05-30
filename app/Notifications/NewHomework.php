<?php
namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewHomework extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'actor'   => $this->post->user->name,
            'message' => 'Nouveau devoir de ' . $this->post->user->name . ($this->post->title ? ' : ' . $this->post->title : '.'),
            'post_id' => $this->post->id,
            'type'    => 'homework',
        ];
    }
}
