<?php
namespace App\Http\Controllers;
use App\Models\{Post,User,Hashtag,Group};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AnalyticsController extends Controller {
    public function school(Request $request) {
        $user = $request->user();
        $schoolId = $user->school_id;

        $dailyPosts = Post::where('school_id',$schoolId)->where('created_at','>=',now()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))->select(DB::raw('DATE(created_at) as day'),DB::raw('COUNT(*) as c'))
            ->pluck('c','day');
        $chartDays = collect(range(29,0))->map(fn($i)=>now()->subDays($i)->format('Y-m-d'));
        $chartData = $chartDays->map(fn($d)=>$dailyPosts[$d]??0)->values();

        $topUsers = User::where('school_id',$schoolId)->withCount('posts')->orderByDesc('posts_count')->take(10)->get();
        $topHashtags = Hashtag::whereHas('posts',fn($q)=>$q->where('school_id',$schoolId))->orderByDesc('posts_count')->take(10)->get();
        $topGroups = Group::where('school_id',$schoolId)->withCount('posts')->orderByDesc('posts_count')->take(5)->with('teacher')->get();

        return view('dashboard.analytics.show', compact('chartData','chartDays','topUsers','topHashtags','topGroups'));
    }
}
