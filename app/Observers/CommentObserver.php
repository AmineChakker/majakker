<?php
namespace App\Observers;

use App\Models\Comment;
use App\Notifications\NewComment;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        $comment->post->increment('comments_count');

        $postAuthor = $comment->post->user;
        if ($postAuthor && $postAuthor->id !== $comment->user_id) {
            $postAuthor->notify(new NewComment($comment));
        }
    }

    public function deleted(Comment $comment): void
    {
        $comment->post->decrement('comments_count');
    }
}
