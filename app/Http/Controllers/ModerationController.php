<?php
namespace App\Http\Controllers;
use App\Models\ModerationReport;
use Illuminate\Http\Request;

class ModerationController extends Controller {
    public function index(Request $request) {
        $tab     = $request->get('tab','pending');
        $reports = ModerationReport::whereHas('post',fn($q)=>$q->where('school_id',$request->user()->school_id))
            ->where('status',$tab)->with('post.user','reporter')->latest()->paginate(20);
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
