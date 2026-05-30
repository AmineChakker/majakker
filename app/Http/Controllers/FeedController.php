<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $user   = $request->user();
        $filter = $request->get('filter', 'all');

        $query = Post::with(['user', 'school', 'group', 'attachments.poll.options', 'reactions', 'hashtags'])
            ->where('school_id', $user->school_id)
            ->whereNull('deleted_at')
            ->latest();

        if ($filter === 'ann')   $query->where(fn($q) => $q->where('is_announcement', true)->orWhereHas('user', fn($q2) => $q2->whereIn('role', ['director', 'teacher'])));
        if ($filter === 'class') $query->whereHas('group', fn($q) => $q->where('kind', 'class'));
        if ($filter === 'clubs') $query->whereHas('group', fn($q) => $q->where('kind', 'club'));

        $posts    = $query->paginate(20);
        $events   = \App\Models\Event::where('school_id', $user->school_id)->where('starts_at', '>=', now())->orderBy('starts_at')->take(4)->get();
        $hashtags = \App\Models\Hashtag::orderByDesc('posts_count')->take(5)->get();
        $groups   = $user->groups()->take(4)->get();

        return view('feed.index', compact('posts', 'events', 'hashtags', 'groups', 'filter'));
    }

    public function homework(Request $request)
    {
        $user = $request->user();

        // Homework = posts with a group attachment, in classes the user belongs to, by teachers/directors
        $groupIds = $user->groups()->where('kind', 'class')->pluck('groups.id');

        $posts = Post::with(['user', 'group', 'attachments'])
            ->where('school_id', $user->school_id)
            ->whereIn('group_id', $groupIds)
            ->whereHas('user', fn($q) => $q->whereIn('role', ['teacher', 'director']))
            ->whereNull('deleted_at')
            ->latest()
            ->paginate(20);

        $groups = $user->groups()->where('kind', 'class')->get();

        return view('homework.index', compact('posts', 'groups'));
    }

    public function api(Request $request)
    {
        $user   = $request->user();
        $cursor = $request->get('cursor');

        $posts = Post::with(['user', 'school', 'group', 'attachments.poll.options', 'reactions', 'hashtags'])
            ->where('school_id', $user->school_id)
            ->whereNull('deleted_at')
            ->when($cursor, fn($q) => $q->where('id', '<', $cursor))
            ->latest()
            ->take(10)
            ->get();

        return response()->json(['posts' => $posts, 'next_cursor' => $posts->last()?->id]);
    }
}
