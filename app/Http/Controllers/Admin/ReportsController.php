<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{School, User, Post, ModerationReport};
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller {
    public function index() {
        $totalSchools  = School::count();
        $totalStudents = User::where('role','student')->count();
        $totalTeachers = User::where('role','teacher')->count();
        $totalPosts    = Post::count();

        $topSchoolsByPosts = School::withCount('posts')->orderByDesc('posts_count')->take(10)->get();
        $topSchoolsByUsers = School::withCount('users')->orderByDesc('users_count')->take(10)->get();

        $modSummary = ModerationReport::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count','status');

        $postsPerWeek = Post::where('created_at','>=',now()->subWeeks(8)->startOfWeek())
            ->groupBy(DB::raw('YEARWEEK(created_at)'))
            ->select(DB::raw('YEARWEEK(created_at) as yw'), DB::raw('COUNT(*) as c'))
            ->orderBy('yw')->pluck('c','yw');

        $weeks = collect(range(7,0))->map(fn($i) => now()->subWeeks($i)->format('oW'));
        $weeklyData = $weeks->map(fn($w) => $postsPerWeek[$w] ?? 0)->values();
        $weekLabels = collect(range(7,0))->map(fn($i) => now()->subWeeks($i)->format('d M'))->values();

        return view('admin.reports.index', compact(
            'totalSchools','totalStudents','totalTeachers','totalPosts',
            'topSchoolsByPosts','topSchoolsByUsers',
            'modSummary','weeklyData','weekLabels'
        ));
    }
}
