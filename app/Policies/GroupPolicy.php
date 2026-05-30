<?php
namespace App\Policies;

use App\Models\{Group, User};

class GroupPolicy
{
    public function manage(User $user, Group $group): bool
    {
        return ($user->isDirector() || ($user->isTeacher() && $user->id === $group->teacher_id))
            && $user->school_id === $group->school_id;
    }
}
