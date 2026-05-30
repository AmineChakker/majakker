<?php
namespace App\Http\Controllers;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller {
    public function indexClasses(Request $request) {
        $groups = $request->user()->groups()->where('kind','class')->with('teacher')->get();
        return view('groups.classes', compact('groups'));
    }
    public function indexClubs(Request $request) {
        $groups = $request->user()->groups()->where('kind','club')->with('teacher')->get();
        $allClubs = Group::where('school_id',$request->user()->school_id)->where('kind','club')->with('teacher')->get();
        return view('groups.clubs', compact('groups','allClubs'));
    }
    public function show(Group $group, Request $request) {
        $user = $request->user();
        $isMember = $group->members()->where('user_id',$user->id)->exists();
        $posts = $group->posts()->with(['user','attachments.poll.options','reactions','hashtags'])->latest()->paginate(20);
        return view('groups.show', compact('group','posts','isMember'));
    }
    public function join(Group $group, Request $request) {
        $group->members()->syncWithoutDetaching([$request->user()->id => ['role'=>'member']]);
        $group->increment('member_count');
        return back()->with('success','Rejoint '.$group->name);
    }
    public function leave(Group $group, Request $request) {
        $group->members()->detach($request->user()->id);
        $group->decrement('member_count');
        return back();
    }
}
