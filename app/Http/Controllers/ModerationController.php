<?php
namespace App\Http\Controllers;
use App\Models\{ModerationReport, Post};
use Illuminate\Http\Request;

class ModerationController extends Controller {

    public function store(Post $post, Request $request)
    {
        $user = $request->user();

        abort_if($post->school_id !== $user->school_id, 403);
        abort_if($post->user_id === $user->id, 403, 'Vous ne pouvez pas signaler votre propre publication.');

        $request->validate(['reason' => 'required|string|max:500']);

        $alreadyPending = ModerationReport::where('post_id', $post->id)
            ->where('reported_by', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return response()->json(['message' => 'Vous avez déjà signalé cette publication.'], 409);
        }

        ModerationReport::create([
            'post_id'     => $post->id,
            'reported_by' => $user->id,
            'reason'      => $request->reason,
            'severity'    => 'medium',
            'status'      => 'pending',
        ]);

        return response()->json(['message' => 'Signalement envoyé. Merci de nous aider à maintenir un espace sûr.']);
    }

    public function index(Request $request) {
        $tab     = $request->get('tab','pending');
        $reports = ModerationReport::whereHas('post',fn($q)=>$q->where('school_id',$request->user()->school_id))
            ->where('status',$tab)->with('post.user','post.group','reporter','reviewer')->latest()->paginate(20);
        return view('dashboard.moderation.index', compact('reports','tab'));
    }
    public function approve(ModerationReport $report) {
        $report->update(['status'=>'approved','reviewed_by'=>auth()->id(),'reviewed_at'=>now()]);
        return back()->with('success','Approuvé');
    }
    public function reject(ModerationReport $report) {
        $report->update(['status'=>'rejected','reviewed_by'=>auth()->id(),'reviewed_at'=>now()]);
        $report->post?->delete();
        return back()->with('success','Contenu supprimé');
    }
    public function ignore(ModerationReport $report) {
        $report->update(['status'=>'ignored','reviewed_by'=>auth()->id(),'reviewed_at'=>now()]);
        return back();
    }
}
