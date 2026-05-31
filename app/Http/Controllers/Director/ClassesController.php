<?php
namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\{Group, Filiere, User};
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $classes  = Group::where('school_id', $schoolId)->where('kind', 'class')
            ->with(['filiere','teacher'])
            ->withCount('members')
            ->orderBy('name')
            ->get();
        $filieres = Filiere::where('school_id', $schoolId)->orderBy('name')->get();
        $teachers = User::where('school_id', $schoolId)->where('role', 'teacher')->orderBy('name')->get();
        return view('dashboard.classes.index', compact('classes','filieres','teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'filiere_id'    => 'nullable|exists:filieres,id',
            'teacher_id'    => 'nullable|exists:users,id',
            'color'         => 'in:blue,saffron,atlas,terracotta',
            'academic_year' => 'nullable|string|max:20',
            'capacity'      => 'nullable|integer|min:1|max:200',
            'description'   => 'nullable|string|max:1000',
        ]);
        Group::create(array_merge(
            $request->only('name','filiere_id','teacher_id','color','academic_year','capacity','description'),
            ['school_id' => $request->user()->school_id, 'kind' => 'class']
        ));
        return back()->with('success', "Classe « {$request->name} » créée.");
    }

    public function update(Request $request, Group $class)
    {
        abort_if($class->school_id !== $request->user()->school_id, 403);
        $request->validate([
            'name'          => 'required|string|max:255',
            'filiere_id'    => 'nullable|exists:filieres,id',
            'teacher_id'    => 'nullable|exists:users,id',
            'color'         => 'in:blue,saffron,atlas,terracotta',
            'academic_year' => 'nullable|string|max:20',
            'capacity'      => 'nullable|integer|min:1|max:200',
            'description'   => 'nullable|string|max:1000',
        ]);
        $class->update($request->only('name','filiere_id','teacher_id','color','academic_year','capacity','description'));
        return back()->with('success', "Classe mise à jour.");
    }

    public function destroy(Group $class, Request $request)
    {
        abort_if($class->school_id !== $request->user()->school_id, 403);
        $class->members()->detach();
        $class->delete();
        return back()->with('success', "Classe supprimée.");
    }
}
