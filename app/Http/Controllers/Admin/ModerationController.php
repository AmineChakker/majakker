<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ModerationReport;
use Illuminate\Http\Request;

class ModerationController extends Controller {
    public function index(Request $request) {
        $tab     = $request->get('tab','pending');
        $reports = ModerationReport::with('post.user.school','reporter')->where('status',$tab)->latest()->paginate(20);
        return view('admin.moderation.index', compact('reports','tab'));
    }
}
