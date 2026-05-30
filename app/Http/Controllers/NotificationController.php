<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function index(Request $request) {
        $notifications = $request->user()->notifications()->latest()->paginate(30);
        return view('notifications.index', compact('notifications'));
    }
    public function markAllRead(Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    }
    public function markRead(Request $request, string $id) {
        $request->user()->notifications()->find($id)?->markAsRead();
        return back();
    }
    public function unreadCount(Request $request) {
        return response()->json(['count' => $request->user()->unreadNotifications->count()]);
    }
}
