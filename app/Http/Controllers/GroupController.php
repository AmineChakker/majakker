<?php
namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function indexClasses(Request $request)
    {
        $groups = $request->user()->groups()->where('kind', 'class')->with('teacher')->get();
        return view('groups.classes', compact('groups'));
    }

    public function indexClubs(Request $request)
    {
        $user      = $request->user();
        $myClubIds = $user->groups()->where('kind', 'club')->pluck('groups.id');
        $allClubs  = Group::where('school_id', $user->school_id)
            ->where('kind', 'club')
            ->with('teacher')
            ->withCount('members')
            ->orderBy('name')
            ->get();
        return view('groups.clubs', compact('allClubs', 'myClubIds'));
    }

    public function show(Group $group, Request $request)
    {
        $user = $request->user();
        abort_if($group->school_id !== $user->school_id, 403);

        $isMember          = $group->members()->where('user_id', $user->id)->exists();
        $isTeacherSupervisor = $user->isTeacher() && $group->teacher_id === $user->id;
        $canViewPosts      = $isMember || $isTeacherSupervisor || $user->canModerate();
        $canPost           = $canViewPosts;

        $posts = $canViewPosts
            ? $group->posts()->with(['user', 'attachments.poll.options', 'reactions', 'hashtags'])->latest()->paginate(20)
            : collect();

        return view('groups.show', compact('group', 'posts', 'isMember', 'canViewPosts', 'canPost'));
    }

    public function join(Group $group, Request $request)
    {
        $user = $request->user();
        abort_if($group->school_id !== $user->school_id, 403);
        abort_if($group->kind !== 'club', 403);

        $alreadyMember = $group->members()->where('user_id', $user->id)->exists();
        if (!$alreadyMember) {
            $group->members()->attach($user->id, ['role' => 'member']);
            $group->increment('member_count');
        }
        return back()->with('success', 'Vous avez rejoint ' . $group->name . ' !');
    }

    public function leave(Group $group, Request $request)
    {
        $user = $request->user();
        abort_if($group->school_id !== $user->school_id, 403);
        abort_if($group->kind !== 'club', 403);

        $wasMember = $group->members()->where('user_id', $user->id)->exists();
        if ($wasMember) {
            $group->members()->detach($user->id);
            $group->decrement('member_count');
        }
        return back();
    }
}
