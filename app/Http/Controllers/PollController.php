<?php
namespace App\Http\Controllers;

use App\Models\{Poll, PollOption, PollVote};
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function vote(Request $request, Poll $poll)
    {
        $request->validate(['option_id' => 'required|exists:poll_options,id']);
        $user = $request->user();

        if (PollVote::where('poll_id', $poll->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['already_voted' => true, 'error' => 'Already voted'], 200);
        }

        $option = PollOption::findOrFail($request->option_id);
        PollVote::create(['poll_id' => $poll->id, 'option_id' => $option->id, 'user_id' => $user->id]);
        $option->increment('votes_count');
        $poll->load('options');

        $totalVotes = $poll->options->sum('votes_count') ?: 1;
        return response()->json([
            'voted'   => true,
            'options' => $poll->options->map(fn($o) => [
                'id'    => $o->id,
                'label' => $o->label,
                'pct'   => round(($o->votes_count / $totalVotes) * 100),
                'votes' => $o->votes_count,
                'mine'  => $o->id === $option->id,
            ]),
        ]);
    }
}
