<?php
namespace App\Observers;

use App\Models\Reaction;
use App\Notifications\NewReaction;

class ReactionObserver
{
    public function created(Reaction $reaction): void
    {
        $col = $reaction->type === 'like' ? 'likes_count' : 'sparks_count';
        $reaction->post->increment($col);

        $postAuthor = $reaction->post->user;
        if ($postAuthor && $postAuthor->id !== $reaction->user_id) {
            $postAuthor->notify(new NewReaction($reaction));
        }
    }

    public function deleted(Reaction $reaction): void
    {
        $col = $reaction->type === 'like' ? 'likes_count' : 'sparks_count';
        $reaction->post->decrement($col);
    }
}
