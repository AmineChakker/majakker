<?php
namespace App\Policies;

use App\Models\{Event, User};

class EventPolicy
{
    public function create(User $user): bool
    {
        return $user->isDirector() || $user->isTeacher();
    }

    public function delete(User $user, Event $event): bool
    {
        return ($user->isDirector() || $user->id === $event->created_by)
            && $user->school_id === $event->school_id;
    }
}
