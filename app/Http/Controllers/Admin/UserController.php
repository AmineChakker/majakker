<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, School};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role', 'tree');

        // Counts per role for tab badges
        $counts = User::selectRaw('role, count(*) as c')
            ->groupBy('role')
            ->pluck('c', 'role');

        if ($role === 'tree') {
            // Tree view: platform admins + schools with nested users
            $admins  = User::where('role', 'admin')->orderBy('name')->get();
            $schools = School::with([
                'users' => fn($q) => $q->with('school')->orderBy('role')->orderBy('name'),
            ])->orderBy('name')->get();

            // Users not assigned to any school (but not admin)
            $orphans = User::whereNull('school_id')
                ->where('role', '!=', 'admin')
                ->orderBy('role')->orderBy('name')->get();

            $allSchools = School::orderBy('name')->get();
            return view('admin.users.index', compact('role','counts','admins','schools','orphans','allSchools'));
        }

        // Flat filtered view
        $users = User::with('school')
            ->when($role !== 'all', fn($q) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        $allSchools = School::orderBy('name')->get();
        return view('admin.users.index', compact('role','counts','users','allSchools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'role'      => 'required|in:admin,director,teacher,student',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'school_id' => in_array($request->role, ['director','teacher','student']) ? $request->school_id : null,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return back()->with('success', "Utilisateur « {$request->name} » créé.");
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required','email', Rule::unique('users','email')->ignore($user->id)],
            'password'  => 'nullable|string|min:8',
            'role'      => 'required|in:admin,director,teacher,student',
            'school_id' => 'nullable|exists:schools,id',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'school_id' => in_array($request->role, ['director','teacher','student']) ? $request->school_id : null,
            'is_active' => $request->boolean('is_active', true),
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', "Utilisateur « {$user->name} » mis à jour.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        $name = $user->name;
        $user->delete();
        return back()->with('success', "Utilisateur « {$name} » supprimé.");
    }
}
