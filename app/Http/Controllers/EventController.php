<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = auth()->user()->events;
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $event = new Event();
        $event->title = $request->title;
        $event->time = $request->time;
        $event->date = $request->date;
        $event->user_id = auth()->id();
        $event->save();

        return redirect()->back()->with('success', 'Event created successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $event = Event::findOrFail($id);
        $event->title = $request->title;
        $event->time = $request->time;
        $event->date = $request->date;
        $event->save();

        return redirect()->back()->with('success', 'Event updated successfully');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->back()->with('success', 'Event deleted successfully');
    }
}
