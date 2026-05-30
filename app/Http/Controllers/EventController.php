<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::where('school_id', $request->user()->school_id)
            ->orderBy('starts_at')->get();
        return view('calendar.show', compact('events'));
    }

    public function store(StoreEventRequest $request)
    {
        Event::create(array_merge(
            $request->validated(),
            [
                'school_id'  => $request->user()->school_id,
                'created_by' => $request->user()->id,
                'color'      => $request->color ?? 'blue',
            ]
        ));
        return back()->with('success', 'Événement créé');
    }

    public function update(StoreEventRequest $request, Event $event)
    {
        $this->authorize('delete', $event); // same gate: only creator or director
        $event->update($request->validated());
        return back()->with('success', 'Événement mis à jour');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();
        return back();
    }
}
