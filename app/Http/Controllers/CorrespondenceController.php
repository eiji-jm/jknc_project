<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Correspondence;
use Illuminate\Support\Facades\Auth;

class CorrespondenceController extends Controller
{
    public function index($type)
    {
        $data = Correspondence::where('type', $type)
            ->orderBy('uploaded_date', 'desc')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'client' => 'nullable|string',
            'tin' => 'nullable|string',
            'subject' => 'required|string',
            'from' => 'nullable|string',
            'to' => 'nullable|string',
            'department' => 'nullable|string',
            'details' => 'nullable|string',
            'date' => 'nullable|date',
            'time' => 'nullable',
            'deadline' => 'nullable|date',
            'sent_via' => 'nullable|string',
        ]);

        $entry = Correspondence::create([
            'type' => $validated['type'],
            'uploaded_date' => now()->toDateString(),
            'user' => optional(Auth::user())->name ?? 'System User',
            'client' => $validated['client'] ?? null,
            'tin' => $validated['tin'] ?? null,
            'subject' => $validated['subject'],
            'from' => $validated['from'] ?? null,
            'to' => $validated['to'] ?? null,
            'department' => $validated['department'] ?? null,
            'details' => $validated['details'] ?? null,
            'date' => $validated['date'] ?? null,
            'time' => $validated['time'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'sent_via' => $validated['sent_via'] ?? 'Email',
            'status' => 'Open',
        ]);

        return response()->json($entry, 201);
    }
}