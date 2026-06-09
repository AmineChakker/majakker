<?php
namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\{Group, User};
use Illuminate\Http\Request;

class ClubsController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $clubs    = Group::where('school_id', $schoolId)->where('kind', 'club')
            ->with('teacher')
            ->withCount('members')
            ->orderBy('name')
            ->get();
        $teachers = User::where('school_id', $schoolId)->where('role', 'teacher')->orderBy('name')->get();
        return view('dashboard.clubs.index', compact('clubs', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'teacher_id'  => 'nullable|exists:users,id',
            'color'       => 'in:blue,saffron,atlas,terracotta',
            'description' => 'nullable|string|max:1000',
        ]);
        Group::create(array_merge(
            $request->only('name', 'teacher_id', 'color', 'description'),
            ['school_id' => $request->user()->school_id, 'kind' => 'club']
        ));
        return back()->with('success', "Club « {$request->name} » créé.");
    }

    public function update(Request $request, Group $club)
    {
        abort_if($club->school_id !== $request->user()->school_id || $club->kind !== 'club', 403);
        $request->validate([
            'name'        => 'required|string|max:255',
            'teacher_id'  => 'nullable|exists:users,id',
            'color'       => 'in:blue,saffron,atlas,terracotta',
            'description' => 'nullable|string|max:1000',
        ]);
        $club->update($request->only('name', 'teacher_id', 'color', 'description'));
        return back()->with('success', "Club mis à jour.");
    }

    public function destroy(Group $club, Request $request)
    {
        abort_if($club->school_id !== $request->user()->school_id || $club->kind !== 'club', 403);
        $club->members()->detach();
        $club->delete();
        return back()->with('success', "Club « {$club->name} » supprimé.");
    }
}
