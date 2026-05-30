<?php
namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Jobs\ModeratePost;
use App\Models\{Post, Hashtag};
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(StorePostRequest $request)
    {
        $user = $request->user();
        $post = Post::create([
            'user_id'         => $user->id,
            'school_id'       => $user->school_id,
            'group_id'        => $request->group_id,
            'body'            => $request->body,
            'title'           => $request->title,
            'is_announcement' => in_array($user->role, ['director', 'teacher']),
            'visibility'      => 'school',
        ]);

        // Attach uploaded files
        if ($request->filled('attachment_ids')) {
            \App\Models\PostAttachment::whereIn('id', $request->attachment_ids)
                ->whereNull('post_id')
                ->update(['post_id' => $post->id]);
        }

        ModeratePost::dispatch($post)->onQueue('default');

        return back()->with('success', 'Publication créée');
    }

    public function show(Post $post)
    {
        $post->load(['user', 'school', 'group', 'attachments.poll.options', 'reactions', 'hashtags', 'comments.user']);
        return view('feed.show', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        $request->validate(['body' => 'required|string|max:5000', 'title' => 'nullable|string|max:255']);
        $post->update($request->only('body', 'title'));
        return back()->with('success', 'Publication mise à jour');
    }

    public function pin(Post $post)
    {
        $this->authorize('pin', $post);
        $post->update(['is_pinned' => !$post->is_pinned]);
        return back();
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return back();
    }
}
