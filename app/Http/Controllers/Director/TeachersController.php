<?php
namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\{User, Group};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeachersController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $teachers = User::where('school_id', $schoolId)->where('role', 'teacher')
            ->with('groups')
            ->orderBy('name')
            ->paginate(30);
        $classes  = Group::where('school_id', $schoolId)->where('kind', 'class')->orderBy('name')->get();
        return view('dashboard.teachers', compact('teachers', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'class_ids'  => 'nullable|array',
            'class_ids.*'=> 'exists:groups,id',
        ]);
        $teacher = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'teacher',
            'school_id' => $request->user()->school_id,
            'is_active' => true,
            'joined_at' => now(),
        ]);
        if ($request->filled('class_ids')) {
            $ids = collect($request->class_ids)->filter();
            foreach ($ids as $gid) {
                $group = Group::find($gid);
                if ($group && $group->school_id === $request->user()->school_id) {
                    $group->members()->syncWithoutDetaching([$teacher->id => ['role' => 'member']]);
                    if (!$group->teacher_id) $group->update(['teacher_id' => $teacher->id]);
                }
            }
        }
        return back()->with('success', "Enseignant « {$teacher->name} » ajouté.")
                     ->with('new_teacher_email', $request->email)
                     ->with('new_teacher_password', $request->password);
    }

    public function update(Request $request, User $teacher)
    {
        abort_if($teacher->school_id !== $request->user()->school_id || $teacher->role !== 'teacher', 403);
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => ['required','email', Rule::unique('users','email')->ignore($teacher->id)],
            'password'   => 'nullable|string|min:8',
            'is_active'  => 'nullable|boolean',
            'class_ids'  => 'nullable|array',
            'class_ids.*'=> 'exists:groups,id',
        ]);
        $data = ['name' => $request->name, 'email' => $request->email, 'is_active' => $request->boolean('is_active', true)];
        if ($request->filled('password')) $data['password'] = Hash::make($request->password);
        $teacher->update($data);

        if ($request->has('class_ids')) {
            $ids = collect($request->class_ids ?? [])->filter()->values();
            $schoolClasses = Group::where('school_id', $request->user()->school_id)
                ->where('kind','class')->pluck('id');
            // Detach from school classes then reattach selected
            $teacher->groups()->detach($schoolClasses);
            foreach ($ids as $gid) {
                $group = Group::find($gid);
                if ($group && $group->school_id === $request->user()->school_id) {
                    $group->members()->syncWithoutDetaching([$teacher->id => ['role' => 'member']]);
                }
            }
        }
        return back()->with('success', "Enseignant mis à jour.");
    }

    public function destroy(User $teacher, Request $request)
    {
        abort_if($teacher->school_id !== $request->user()->school_id || $teacher->role !== 'teacher', 403);
        Group::where('teacher_id', $teacher->id)->update(['teacher_id' => null]);
        $teacher->groups()->detach();
        $teacher->delete();
        return back()->with('success', "Enseignant supprimé.");
    }
}
