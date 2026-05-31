<?php
namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use Illuminate\Http\Request;

class FilieresController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $filieres = Filiere::where('school_id', $schoolId)
            ->withCount('classes')
            ->orderBy('level')->orderBy('name')
            ->get();
        return view('dashboard.filieres.index', compact('filieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:20',
            'level'       => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'color'       => 'in:blue,saffron,atlas,terracotta',
        ]);
        Filiere::create(array_merge(
            $request->only('name','code','level','description','color'),
            ['school_id' => $request->user()->school_id]
        ));
        return back()->with('success', "Filière « {$request->name} » créée.");
    }

    public function update(Request $request, Filiere $filiere)
    {
        $this->authorizeSchool($filiere, $request);
        $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:20',
            'level'       => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'color'       => 'in:blue,saffron,atlas,terracotta',
        ]);
        $filiere->update($request->only('name','code','level','description','color'));
        return back()->with('success', "Filière mise à jour.");
    }

    public function destroy(Filiere $filiere, Request $request)
    {
        $this->authorizeSchool($filiere, $request);
        $filiere->delete();
        return back()->with('success', "Filière supprimée.");
    }

    private function authorizeSchool(Filiere $filiere, Request $request): void
    {
        abort_if($filiere->school_id !== $request->user()->school_id, 403);
    }
}
