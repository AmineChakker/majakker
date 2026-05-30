<?php
namespace App\Policies;

use App\Models\{Comment, User};

class CommentPolicy
{
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id
            || $user->isDirector()
            || $user->isAdmin();
    }
}
