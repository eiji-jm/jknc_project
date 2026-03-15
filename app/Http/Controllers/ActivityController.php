<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Event;
use App\Models\Call;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\Note;

class ActivityController extends Controller
{
    public function index()
    {
        // Dynamically sweep and update 'upcoming' meetings to 'completed' if time passed
        $meetings = Meeting::where('status', 'upcoming')->get();
        foreach ($meetings as $meeting) {
            if ($meeting->date && $meeting->time) {
                try {
                    $start = Carbon::parse($meeting->date . ' ' . $meeting->time);
                    
                    // Parse duration loosely (e.g. '30 mins', '1 hour', '1 hr 30 mins')
                    $durationMinutes = 0;
                    if ($meeting->duration) {
                        preg_match_all('/(\d+)\s*(hr|hour|min|m)/i', $meeting->duration, $matches, PREG_SET_ORDER);
                        if (!empty($matches)) {
                            foreach ($matches as $match) {
                                $val = (int)$match[1];
                                $unit = strtolower($match[2]);
                                if (str_starts_with($unit, 'h')) {
                                    $durationMinutes += $val * 60;
                                } else {
                                    $durationMinutes += $val;
                                }
                            }
                        } else {
                            // Fallback if just raw number provided
                            preg_match('/\d+/', $meeting->duration, $m);
                            $durationMinutes = !empty($m[0]) ? (int)$m[0] : 30;
                        }
                    } else {
                        $durationMinutes = 30; // default to 30 mins if not provided
                    }
                    
                    $end = $start->copy()->addMinutes($durationMinutes);
                    
                    if (now()->greaterThanOrEqualTo($end)) {
                        $meeting->status = 'completed';
                        $meeting->save();
                    }
                } catch (\Exception $e) {
                    // Ignore malformed datetimes
                }
            }
        }

        return response()->json([
            'tasks' => Task::with('notes')->latest()->get(),
            'events' => Event::with('notes')->latest()->get(),
            'calls' => Call::with('notes')->latest()->get(),
            'meetings' => Meeting::with('notes')->latest()->get(),
            'users' => User::pluck('email'),
        ]);
    }

    public function storeNote(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'owner' => 'nullable|string',
            'noteable_id' => 'required|integer',
            'noteable_type' => 'required|string',
        ]);

        // Standardize noteable_type to full namespace
        if (!str_contains($validated['noteable_type'], 'App\\Models\\')) {
            $validated['noteable_type'] = 'App\\Models\\' . ucfirst($validated['noteable_type']);
        }

        $note = Note::create($validated);
        return response()->json($note);
    }

    public function updateNote(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $note = Note::findOrFail($id);
        $note->update($validated);
        return response()->json($note);
    }

    public function destroyNote($id)
    {
        Note::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function processMeeting($id)
    {
        $meeting = Meeting::findOrFail($id);
        
        // Mark as having transcript and minutes
        $meeting->has_transcript = true;
        $meeting->has_minutes = true;
        $meeting->save();

        // Simulate producing a transcript note
        Note::create([
            'content' => "AI TRANSCRIPT SUMMARY:\n\nParticipants discussed the Q3 project timeline. John mentioned that the backend is 80% complete. Sarah raised concerns about the UI polish. Action items were assigned to the design team.",
            'owner' => 'AI Assistant',
            'noteable_id' => $meeting->id,
            'noteable_type' => Meeting::class
        ]);

        // Simulate producing a minutes note
        Note::create([
            'content' => "MEETING MINUTES:\n\n1. Project Status: Backend on track for next week.\n2. UI/UX: Design team to review user feedback by Wednesday.\n3. Next Steps: Schedule a follow-up for Friday 2 PM.",
            'owner' => 'AI Assistant',
            'noteable_id' => $meeting->id,
            'noteable_type' => Meeting::class
        ]);

        return response()->json($meeting->load('notes'));
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

        // Calculate initial status based on date/time scheduling
        if (empty($validated['status']) || $validated['status'] === 'upcoming') {
            if (!empty($validated['date']) && !empty($validated['time'])) {
                try {
                    $start = Carbon::parse($validated['date'] . ' ' . $validated['time']);
                    
                    $durationMinutes = 0;
                    if (!empty($validated['duration'])) {
                        preg_match_all('/(\d+)\s*(hr|hour|min|m)/i', $validated['duration'], $matches, PREG_SET_ORDER);
                        if (!empty($matches)) {
                            foreach ($matches as $match) {
                                $durationMinutes += str_starts_with(strtolower($match[2]), 'h') ? (int)$match[1] * 60 : (int)$match[1];
                            }
                        } else {
                            preg_match('/\d+/', $validated['duration'], $m);
                            $durationMinutes = !empty($m[0]) ? (int)$m[0] : 30;
                        }
                    } else {
                        $durationMinutes = 30;
                    }

                    $end = $start->copy()->addMinutes($durationMinutes);
                    $validated['status'] = now()->greaterThanOrEqualTo($end) ? 'completed' : 'upcoming';
                } catch (\Exception $e) {
                    $validated['status'] = 'upcoming';
                }
            } else {
                $validated['status'] = 'upcoming';
            }
        }

        $meeting = Meeting::create($validated);
        return response()->json($meeting);
    }

    public function updateTask(Request $request, $id)
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

        $task = Task::findOrFail($id);
        $task->update($validated);
        return response()->json($task);
    }

    public function updateEvent(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'from' => 'nullable|string',
            'to' => 'nullable|string',
            'related_to' => 'nullable|string',
            'host' => 'nullable|string',
        ]);

        $event = Event::findOrFail($id);
        $event->update($validated);
        return response()->json($event);
    }

    public function updateCall(Request $request, $id)
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

        $call = Call::findOrFail($id);
        $call->update($validated);
        return response()->json($call);
    }

    public function updateMeeting(Request $request, $id)
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

        $meeting = Meeting::findOrFail($id);

        // Recalculate status during updates
        if (empty($validated['status']) || $validated['status'] === 'upcoming') {
            if (!empty($validated['date']) && !empty($validated['time'])) {
                try {
                    $start = Carbon::parse($validated['date'] . ' ' . $validated['time']);
                    
                    $durationMinutes = 0;
                    if (!empty($validated['duration'])) {
                        preg_match_all('/(\d+)\s*(hr|hour|min|m)/i', $validated['duration'], $matches, PREG_SET_ORDER);
                        if (!empty($matches)) {
                            foreach ($matches as $match) {
                                $durationMinutes += str_starts_with(strtolower($match[2]), 'h') ? (int)$match[1] * 60 : (int)$match[1];
                            }
                        } else {
                            preg_match('/\d+/', $validated['duration'], $m);
                            $durationMinutes = !empty($m[0]) ? (int)$m[0] : 30;
                        }
                    } else {
                        $durationMinutes = 30;
                    }

                    $end = $start->copy()->addMinutes($durationMinutes);
                    $validated['status'] = now()->greaterThanOrEqualTo($end) ? 'completed' : 'upcoming';
                } catch (\Exception $e) {
                    // Do nothing
                }
            }
        }

        $meeting->update($validated);
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
