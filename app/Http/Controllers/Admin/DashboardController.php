<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{School,User,Post,ModerationReport};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function show() {
        $schoolCount   = School::count();
        $studentCount  = User::where('role','student')->count();
        $teacherCount  = User::where('role','teacher')->count();
        $pendingMod    = ModerationReport::where('status','pending')->count();

        // Activity for chart (30 days)
        $chartData = Post::where('created_at','>=',now()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))->orderBy(DB::raw('DATE(created_at)'))
            ->select(DB::raw('DATE(created_at) as day'),DB::raw('COUNT(*) as count'))
            ->pluck('count','day');
        $chartDays = collect(range(29,0))->map(fn($i)=>now()->subDays($i)->format('Y-m-d'));
        $activityData = $chartDays->map(fn($d)=>$chartData[$d]??0)->values();

        // Top schools by activity
        $topSchools = School::withCount('posts')->orderByDesc('posts_count')->take(5)->get();

        // Recent signups
        $recentSchools = School::latest()->take(5)->get();

        // Moderation
        $reports = ModerationReport::with('post.user','reporter')->where('status','pending')->latest()->take(4)->get();

        // Revenue (mock data — real app would integrate billing)
        $revenue = ['mrr'=>412000,'mom_pct'=>18,'segments'=>[
            ['label'=>'Pro · Lycées','amount'=>248000,'color'=>'#7E5BEF'],
            ['label'=>'School · Collèges','amount'=>102000,'color'=>'#2563EB'],
            ['label'=>'Starter','amount'=>38000,'color'=>'#06B6D4'],
            ['label'=>'Add-ons','amount'=>24000,'color'=>'#10B981'],
        ]];

        return view('admin.dashboard.show', compact(
            'schoolCount','studentCount','teacherCount','pendingMod',
            'activityData','chartDays','topSchools','recentSchools','reports','revenue'
        ));
    }
}
