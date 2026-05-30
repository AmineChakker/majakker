<?php
namespace App\Http\Controllers;
use App\Models\{Post,User,Hashtag};
use Illuminate\Http\Request;

class SearchController extends Controller {
    public function index(Request $request) {
        $q = $request->get('q','');
        $posts = $q ? Post::with(['user','attachments.poll.options','reactions','hashtags'])
            ->where('school_id',$request->user()->school_id)
            ->where(fn($sq)=>$sq->where('body','like',"%$q%")->orWhere('title','like',"%$q%"))
            ->latest()->take(20)->get() : collect();
        $users = $q ? User::where('school_id',$request->user()->school_id)
            ->where(fn($sq)=>$sq->where('name','like',"%$q%")->orWhere('email','like',"%$q%"))
            ->take(10)->get() : collect();
        $hashtags = $q ? Hashtag::where('name','like',"%$q%")->orderByDesc('posts_count')->take(8)->get() : collect();
        return view('search.index', compact('posts','users','hashtags','q'));
    }
}
