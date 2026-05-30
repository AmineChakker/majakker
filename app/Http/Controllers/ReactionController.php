<?php
namespace App\Http\Controllers;
use App\Models\{Post,Reaction};
use Illuminate\Http\Request;

class ReactionController extends Controller {
    public function toggle(Request $request, Post $post) {
        $request->validate(['type' => 'in:like,spark,check']);
        $user = $request->user();
        $type = $request->input('type','like');
        $existing = Reaction::where('user_id',$user->id)->where('post_id',$post->id)->where('type',$type)->first();
        if ($existing) {
            $existing->delete();
            $col = $type==='like'?'likes_count':'sparks_count';
            $post->decrement($col);
            return response()->json(['reacted'=>false, 'count'=>$post->fresh()->$col]);
        }
        Reaction::create(['user_id'=>$user->id,'post_id'=>$post->id,'type'=>$type]);
        $col = $type==='like'?'likes_count':'sparks_count';
        $post->increment($col);
        return response()->json(['reacted'=>true,'count'=>$post->fresh()->$col]);
    }
}
