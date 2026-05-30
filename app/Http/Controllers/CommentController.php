<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\{Post, Comment};

class CommentController extends Controller
{
    public function index(Post $post)
    {
        return response()->json(
            $post->comments()->with('user', 'replies.user')->whereNull('parent_id')->get()
        );
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        $post->comments()->create([
            'user_id'   => $request->user()->id,
            'body'      => $request->body,
            'parent_id' => $request->parent_id,
        ]);
        return back();
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return back();
    }
}
