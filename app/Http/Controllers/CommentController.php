<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\{Post, Comment};
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->with('user')
            ->whereNull('parent_id')
            ->latest()
            ->get()
            ->map(fn($c) => [
                'id'         => $c->id,
                'body'       => $c->body,
                'created_at' => $c->created_at->toISOString(),
                'user'       => ['id' => $c->user->id, 'name' => $c->user->name],
            ]);

        return response()->json($comments);
    }

    public function store(Request $request, Post $post)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $comment = $post->comments()->create([
            'user_id'   => $request->user()->id,
            'body'      => $request->body,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $comment->load('user');

        // If JSON request (from Alpine.js), return JSON
        if ($request->expectsJson() || $request->wantsJson() || $request->header('Content-Type') === 'application/json') {
            return response()->json([
                'id'         => $comment->id,
                'body'       => $comment->body,
                'created_at' => $comment->created_at->toISOString(),
                'user'       => ['id' => $comment->user->id, 'name' => $comment->user->name],
            ]);
        }

        return back();
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return back();
    }
}
