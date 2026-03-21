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
        $request->validate([
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
            'type' => $request->type,
            'uploaded_date' => now()->toDateString(),
            'user' => Auth::user()->name,
            'client' => $request->client,
            'tin' => $request->tin,
            'subject' => $request->subject,
            'from' => $request->from,
            'to' => $request->to,
            'department' => $request->department,
            'details' => $request->details,
            'date' => $request->date,
            'time' => $request->time,
            'deadline' => $request->deadline,
            'sent_via' => $request->sent_via ?? 'Email',
            'status' => 'Open',
        ]);

        return response()->json($entry);
    }
}