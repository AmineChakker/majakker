<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{School, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::withCount('users', 'posts')
            ->with('director')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admin.schools.index', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'city'             => 'required|string|max:100',
            'plan'             => 'required|in:starter,school,pro',
            'student_count'    => 'nullable|integer|min:0',
            'director_name'    => 'required|string|max:255',
            'director_email'   => 'required|email|unique:users,email',
            'director_password'=> 'required|string|min:8',
        ]);

        $school = School::create([
            'name'          => $request->name,
            'city'          => $request->city,
            'plan'          => $request->plan,
            'student_count' => $request->student_count ?? 0,
            'initial'       => strtoupper(substr($request->name, 0, 1)),
            'is_active'     => true,
            'joined_at'     => now(),
        ]);

        User::create([
            'name'      => $request->director_name,
            'email'     => $request->director_email,
            'password'  => Hash::make($request->director_password),
            'role'      => 'director',
            'school_id' => $school->id,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return redirect()->route('admin.schools')
            ->with('created_school', $school->name)
            ->with('created_director_email', $request->director_email)
            ->with('created_director_password', $request->director_password);
    }

    public function edit(School $school)
    {
        $school->load('director');
        return view('admin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'city'          => 'required|string|max:100',
            'plan'          => 'required|in:starter,school,pro',
            'student_count' => 'nullable|integer|min:0',
            'is_active'     => 'nullable|boolean',
            // Director fields (optional — only update if provided)
            'director_name'    => 'nullable|string|max:255',
            'director_email'   => 'nullable|email|unique:users,email,' . ($school->director?->id ?? 'NULL'),
            'director_password'=> 'nullable|string|min:8',
        ]);

        $school->update([
            'name'          => $request->name,
            'city'          => $request->city,
            'plan'          => $request->plan,
            'student_count' => $request->student_count ?? $school->student_count,
            'initial'       => strtoupper(substr($request->name, 0, 1)),
            'is_active'     => $request->boolean('is_active', true),
        ]);

        if ($school->director) {
            $directorData = array_filter([
                'name'  => $request->director_name  ?: null,
                'email' => $request->director_email ?: null,
            ]);
            if ($request->filled('director_password')) {
                $directorData['password'] = Hash::make($request->director_password);
            }
            if (!empty($directorData)) {
                $school->director->update($directorData);
            }
        } elseif ($request->filled('director_name') && $request->filled('director_email')) {
            // Create director if none exists yet
            User::create([
                'name'      => $request->director_name,
                'email'     => $request->director_email,
                'password'  => Hash::make($request->director_password ?? Str::random(12)),
                'role'      => 'director',
                'school_id' => $school->id,
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('admin.schools')->with('success', "École « {$school->name} » mise à jour.");
    }

    public function destroy(School $school)
    {
        // Soft-cascade: detach users (don't delete them) and delete school
        User::where('school_id', $school->id)->update(['school_id' => null]);
        $school->delete();
        return redirect()->route('admin.schools')->with('success', "École supprimée.");
    }
}
