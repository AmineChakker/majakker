<?php
namespace App\Http\Controllers;

use App\Models\{Post, User, ModerationReport, Event, Group};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $user   = $request->user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('admin.dashboard');
        }

        $schoolId   = $school->id;
        $weekNumber = now()->weekOfYear;

        $activeStudents = User::where('school_id', $schoolId)->where('role', 'student')->where('is_active', true)->count();
        $totalStudents  = User::where('school_id', $schoolId)->where('role', 'student')->count();
        $suspendedStudents = User::where('school_id', $schoolId)->where('role', 'student')->where('is_active', false)->count();
        $activeTeachers = User::where('school_id', $schoolId)->where('role', 'teacher')->where('is_active', true)->count();
        $totalTeachers  = User::where('school_id', $schoolId)->where('role', 'teacher')->count();
        $postsThisWeek  = Post::where('school_id', $schoolId)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $postsLastWeek  = Post::where('school_id', $schoolId)->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        $pendingReports = ModerationReport::whereHas('post', fn($q) => $q->where('school_id', $schoolId))->where('status', 'pending')->count();

        $activityData = Post::where('school_id', $schoolId)
            ->where('created_at', '>=', now()->subDays(14)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))->orderBy(DB::raw('DATE(created_at)'))
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as count'))
            ->pluck('count', 'day')->toArray();
        $chartDays = collect(range(14, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
        $chartData  = $chartDays->map(fn($d) => $activityData[$d] ?? 0)->values();

        $reports = ModerationReport::whereHas('post', fn($q) => $q->where('school_id', $schoolId))
            ->where('status', 'pending')->with('post.user', 'reporter')->latest()->take(4)->get();

        $classes = Group::where('school_id', $schoolId)->where('kind', 'class')->with('teacher')
            ->withCount('posts')->orderByDesc('posts_count')->take(4)->get();

        $events = Event::where('school_id', $schoolId)->where('starts_at', '>=', now())->orderBy('starts_at')->take(4)->get();

        $totalReactions = \App\Models\Reaction::whereHas('post', fn($q) => $q->where('school_id', $schoolId))->count();
        $totalPosts     = Post::where('school_id', $schoolId)->count() ?: 1;
        $engagementPct  = min(100, round(($totalReactions / ($totalPosts * 3)) * 100));
        $clubPosts      = Post::where('school_id', $schoolId)->whereHas('group', fn($q) => $q->where('kind', 'club'))->count();
        $clubPct        = $totalPosts > 0 ? min(100, round(($clubPosts / $totalPosts) * 100)) : 0;

        return view('dashboard.show', compact(
            'school', 'weekNumber',
            'activeStudents', 'totalStudents', 'suspendedStudents', 'activeTeachers', 'totalTeachers', 'postsThisWeek', 'postsLastWeek',
            'pendingReports', 'reports',
            'chartData', 'chartDays',
            'classes', 'events',
            'engagementPct', 'clubPct'
        ));
    }

    // ── Teachers ────────────────────────────────────────────────────────────

    public function teachers(Request $request)
    {
        $schoolId = $request->user()->school_id;
        if (!$schoolId) return redirect()->route('admin.dashboard');

        $teachers = User::where('school_id', $schoolId)->where('role', 'teacher')->with('groups')->paginate(20);
        return view('dashboard.teachers', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        $schoolId = $request->user()->school_id;
        if (!$schoolId) return back()->with('error', 'Aucune école associée.');

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'teacher',
            'school_id' => $schoolId,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return back()->with('success', "Enseignant « {$request->name} » créé.");
    }

    public function updateTeacher(Request $request, User $user)
    {
        if ($user->role !== 'teacher' || $user->school_id !== $request->user()->school_id) {
            return back()->with('error', 'Action non autorisée.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
        ]);

        $data = ['name' => $request->name, 'email' => $request->email];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', "Enseignant « {$user->name} » mis à jour.");
    }

    public function destroyTeacher(User $user, Request $request)
    {
        if ($user->role !== 'teacher' || $user->school_id !== $request->user()->school_id) {
            return back()->with('error', 'Action non autorisée.');
        }

        $name = $user->name;
        $user->delete();
        return back()->with('success', "Enseignant « {$name} » supprimé.");
    }

    // ── Students ────────────────────────────────────────────────────────────

    public function students(Request $request)
    {
        $schoolId = $request->user()->school_id;
        if (!$schoolId) return redirect()->route('admin.dashboard');

        $students = User::where('school_id', $schoolId)->where('role', 'student')->with('groups')->paginate(30);
        return view('dashboard.students', compact('students'));
    }

    public function storeStudent(Request $request)
    {
        $schoolId = $request->user()->school_id;
        if (!$schoolId) return back()->with('error', 'Aucune école associée.');

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'student',
            'school_id' => $schoolId,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return back()->with('success', "Élève « {$request->name} » créé.");
    }

    public function updateStudent(Request $request, User $user)
    {
        if ($user->role !== 'student' || $user->school_id !== $request->user()->school_id) {
            return back()->with('error', 'Action non autorisée.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
        ]);

        $data = ['name' => $request->name, 'email' => $request->email];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', "Élève « {$user->name} » mis à jour.");
    }

    public function destroyStudent(User $user, Request $request)
    {
        if ($user->role !== 'student' || $user->school_id !== $request->user()->school_id) {
            return back()->with('error', 'Action non autorisée.');
        }

        $name = $user->name;
        $user->delete();
        return back()->with('success', "Élève « {$name} » supprimé.");
    }

    public function suspendStudent(User $user, Request $request)
    {
        if ($user->role !== 'student' || $user->school_id !== $request->user()->school_id) {
            return back()->with('error', 'Action non autorisée.');
        }

        $user->update(['is_active' => false]);
        return back()->with('success', "{$user->name} suspendu.");
    }

    public function unsuspendStudent(User $user, Request $request)
    {
        if ($user->role !== 'student' || $user->school_id !== $request->user()->school_id) {
            return back()->with('error', 'Action non autorisée.');
        }

        $user->update(['is_active' => true]);
        return back()->with('success', "Compte de {$user->name} rétabli.");
    }
}
