<?php
namespace App\Policies;

use App\Models\{Post, User};

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id
            || $user->isDirector()
            || $user->isAdmin();
    }

    public function pin(User $user, Post $post): bool
    {
        return ($user->isDirector() || $user->isTeacher())
            && $user->school_id === $post->school_id;
    }
}
