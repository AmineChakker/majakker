<?php
namespace App\Http\Controllers;

use App\Jobs\ModeratePost;
use App\Models\{Post, PostAttachment, Hashtag};
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'body'             => 'nullable|string|max:5000',
            'title'            => 'nullable|string|max:255',
            'group_id'         => 'nullable|integer|exists:groups,id',
            'attachment_ids'   => 'nullable|array',
            'attachment_ids.*' => 'integer|exists:post_attachments,id',
        ]);

        // At least body or attachment required
        if (empty(trim($request->body ?? '')) && empty($request->attachment_ids)) {
            return response()->json(['errors' => ['body' => ['Le message ou une pièce jointe est requis.']]], 422);
        }

        $user = $request->user();
        $post = Post::create([
            'user_id'         => $user->id,
            'school_id'       => $user->school_id,
            'group_id'        => $request->group_id,
            'body'            => $request->body ?? '',
            'title'           => $request->title,
            'is_announcement' => in_array($user->role, ['director', 'teacher']),
            'visibility'      => 'school',
        ]);

        // Link uploaded attachments to this post
        if (!empty($request->attachment_ids)) {
            PostAttachment::whereIn('id', $request->attachment_ids)
                ->whereNull('post_id')
                ->update(['post_id' => $post->id]);
        }

        ModeratePost::dispatch($post)->onQueue('default');

        // doSubmit() fetches this endpoint and expects a redirect or JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'redirect' => route('feed')]);
        }

        return redirect()->route('feed')->with('success', 'Publication créée');
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
