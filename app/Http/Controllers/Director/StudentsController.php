<?php
namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\{User, Group};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentsController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $classFilter = $request->get('class_id');

        $students = User::where('school_id', $schoolId)->where('role', 'student')
            ->when($classFilter, fn($q) => $q->whereHas('groups', fn($g) => $g->where('groups.id', $classFilter)))
            ->with(['groups' => fn($q) => $q->where('kind','class')])
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        $classes = Group::where('school_id', $schoolId)->where('kind', 'class')
            ->orderBy('name')->get();

        return view('dashboard.students', compact('students', 'classes', 'classFilter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'class_id'  => 'nullable|exists:groups,id',
        ]);
        $student = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'student',
            'school_id' => $request->user()->school_id,
            'is_active' => true,
            'joined_at' => now(),
        ]);
        if ($request->filled('class_id')) {
            $group = Group::find($request->class_id);
            if ($group && $group->school_id === $request->user()->school_id) {
                $group->members()->syncWithoutDetaching([$student->id => ['role' => 'member']]);
                $group->increment('member_count');
            }
        }
        return back()->with('success', "Élève « {$student->name} » ajouté.")
                     ->with('new_student_email', $request->email)
                     ->with('new_student_password', $request->password);
    }

    public function update(Request $request, User $student)
    {
        abort_if($student->school_id !== $request->user()->school_id || $student->role !== 'student', 403);
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required','email', Rule::unique('users','email')->ignore($student->id)],
            'password' => 'nullable|string|min:8',
            'class_id' => 'nullable|exists:groups,id',
            'is_active'=> 'nullable|boolean',
        ]);
        $data = ['name' => $request->name, 'email' => $request->email, 'is_active' => $request->boolean('is_active', true)];
        if ($request->filled('password')) $data['password'] = Hash::make($request->password);
        $student->update($data);

        if ($request->has('class_id')) {
            $schoolClasses = Group::where('school_id', $request->user()->school_id)
                ->where('kind','class')->pluck('id');
            $student->groups()->detach($schoolClasses);
            if ($request->filled('class_id')) {
                $group = Group::find($request->class_id);
                if ($group && $group->school_id === $request->user()->school_id) {
                    $group->members()->syncWithoutDetaching([$student->id => ['role' => 'member']]);
                }
            }
        }
        return back()->with('success', "Élève mis à jour.");
    }

    public function destroy(User $student, Request $request)
    {
        abort_if($student->school_id !== $request->user()->school_id || $student->role !== 'student', 403);
        $student->groups()->detach();
        $student->delete();
        return back()->with('success', "Élève supprimé.");
    }
}
