<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Event;
use App\Models\Call;
use App\Models\Meeting;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        return response()->json([
            'tasks' => Task::latest()->get(),
            'events' => Event::latest()->get(),
            'calls' => Call::latest()->get(),
            'meetings' => Meeting::latest()->get(),
        ]);
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'due_date' => 'nullable|string',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'related_to' => 'nullable|string',
            'owner' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $task = Task::create($validated);
        return response()->json($task);
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'from' => 'nullable|string',
            'to' => 'nullable|string',
            'related_to' => 'nullable|string',
            'host' => 'nullable|string',
        ]);

        $event = Event::create($validated);
        return response()->json($event);
    }

    public function storeCall(Request $request)
    {
        $validated = $request->validate([
            'contact' => 'required|string',
            'type' => 'nullable|string',
            'start_time' => 'nullable|string',
            'start_hour' => 'nullable|string',
            'duration' => 'nullable|string',
            'related_to' => 'nullable|string',
            'owner' => 'nullable|string',
            'completed' => 'nullable|boolean',
            'purpose' => 'nullable|string',
            'agenda' => 'nullable|string',
        ]);

        $call = Call::create($validated);
        return response()->json($call);
    }

    public function storeMeeting(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'owner' => 'nullable|string',
            'date' => 'nullable|string',
            'time' => 'nullable|string',
            'duration' => 'nullable|string',
            'location' => 'nullable|string',
            'attendees' => 'nullable|numeric',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
            'has_video' => 'nullable|boolean',
            'has_audio' => 'nullable|boolean',
            'has_transcript' => 'nullable|boolean',
            'has_minutes' => 'nullable|boolean',
        ]);

        $meeting = Meeting::create($validated);
        return response()->json($meeting);
    }

    public function destroyTask($id)
    {
        Task::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function destroyEvent($id)
    {
        Event::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function destroyCall($id)
    {
        Call::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function destroyMeeting($id)
    {
        Meeting::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
