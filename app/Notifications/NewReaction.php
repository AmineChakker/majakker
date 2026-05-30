<?php
namespace App\Notifications;

use App\Models\Reaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewReaction extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reaction $reaction) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $labels = ['like' => 'aimé', 'spark' => 'sparké', 'check' => 'vérifié'];
        return [
            'actor'   => $this->reaction->user->name,
            'message' => $this->reaction->user->name . ' a ' . ($labels[$this->reaction->type] ?? 'réagi à') . ' votre publication.',
            'post_id' => $this->reaction->post_id,
            'type'    => 'reaction',
        ];
    }
}
