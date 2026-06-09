<?php
namespace App\Http\Controllers;

use App\Models\{Post, User, ModerationReport, Event, Group, Message};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $user   = $request->user();
        $school = $user->school;

        if (!$school) {
            // Admin has no school — redirect to admin dashboard
            return redirect()->route('admin.dashboard');
        }

        $schoolId   = $school->id;
        $weekNumber = now()->weekOfYear;

        $activeStudents = User::where('school_id', $schoolId)->where('role', 'student')->where('is_active', true)->count();
        $totalStudents  = User::where('school_id', $schoolId)->where('role', 'student')->count();
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

        // ── Messages ─────────────────────────────────────────────────────────
        $unreadTotal = Message::where('recipient_id', $user->id)->whereNull('read_at')->count();

        $recentConversations = User::whereIn('id', function ($q) use ($user) {
            $q->select(DB::raw('CASE WHEN sender_id = '.$user->id.' THEN recipient_id ELSE sender_id END'))
              ->from('messages')
              ->where(fn($q2) => $q2->where('sender_id', $user->id)->orWhere('recipient_id', $user->id));
        })->get()->map(function ($u) use ($user) {
            $u->last_message = Message::where(fn($q) => $q->where('sender_id', $user->id)->where('recipient_id', $u->id))
                ->orWhere(fn($q) => $q->where('sender_id', $u->id)->where('recipient_id', $user->id))
                ->latest()->first();
            $u->unread_count = Message::where('sender_id', $u->id)->where('recipient_id', $user->id)->whereNull('read_at')->count();
            return $u;
        })->sortByDesc(fn($u) => $u->last_message?->created_at)->take(5)->values();

        return view('dashboard.show', compact(
            'school', 'weekNumber',
            'activeStudents', 'totalStudents', 'postsThisWeek', 'postsLastWeek',
            'pendingReports', 'reports',
            'chartData', 'chartDays',
            'classes', 'events',
            'engagementPct', 'clubPct',
            'unreadTotal', 'recentConversations'
        ));
    }

    public function teachers(Request $request)
    {
        $schoolId = $request->user()->school_id;
        if (!$schoolId) return redirect()->route('admin.dashboard');

        $teachers = User::where('school_id', $schoolId)->where('role', 'teacher')->with('groups')->paginate(20);
        return view('dashboard.teachers', compact('teachers'));
    }

    public function students(Request $request)
    {
        $schoolId = $request->user()->school_id;
        if (!$schoolId) return redirect()->route('admin.dashboard');

        $students = User::where('school_id', $schoolId)->where('role', 'student')->with('groups')->paginate(30);
        return view('dashboard.students', compact('students'));
    }
}
