<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\{Message, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $conversations = User::whereIn('id', function ($q) use ($user) {
            $q->select(DB::raw('CASE WHEN sender_id = ' . $user->id . ' THEN recipient_id ELSE sender_id END'))
              ->from('messages')
              ->where(fn($q2) => $q2->where('sender_id', $user->id)->orWhere('recipient_id', $user->id));
        })->get()->map(function ($u) use ($user) {
            $u->last_message = Message::where(fn($q) => $q->where('sender_id', $user->id)->where('recipient_id', $u->id))
                ->orWhere(fn($q) => $q->where('sender_id', $u->id)->where('recipient_id', $user->id))
                ->latest()->first();
            return $u;
        })->sortByDesc(fn($u) => $u->last_message?->created_at);

        return view('messages.index', compact('conversations'));
    }

    public function conversation(User $user, Request $request)
    {
        $me = $request->user();
        $messages = Message::where(fn($q) => $q->where('sender_id', $me->id)->where('recipient_id', $user->id))
            ->orWhere(fn($q) => $q->where('sender_id', $user->id)->where('recipient_id', $me->id))
            ->oldest()->get();

        Message::where('sender_id', $user->id)->where('recipient_id', $me->id)
            ->whereNull('read_at')->update(['read_at' => now()]);

        return view('messages.show', compact('user', 'messages'));
    }

    public function send(User $recipient, StoreMessageRequest $request)
    {
        Message::create([
            'sender_id'    => $request->user()->id,
            'recipient_id' => $recipient->id,
            'body'         => $request->body,
        ]);
        return back();
    }
}
