<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Correspondence;
use Illuminate\Support\Facades\Auth;

class CorrespondenceController extends Controller
{
    // Get all correspondences by type
    public function index($type)
    {
        $data = Correspondence::where('type', $type)
                ->orderBy('uploaded_date', 'desc')
                ->get();

        return response()->json($data);
    }

    // Save a new correspondence
    public function store(Request $request)
    {
        $entry = Correspondence::create([
            'type' => $request->type,
            'uploaded_date' => now()->toDateString(),
            'user' => Auth::user()->name, // logged-in user
            'client' => $request->client,
            'tin' => $request->tin,
            'subject' => $request->subject,
            'from' => $request->from,
            'to' => $request->to,
            'department' => $request->department,
            'date' => $request->date,
            'time' => $request->time,
            'deadline' => $request->deadline,
            'period' => $request->period,
            'response_date' => $request->response_date,
            'sent_via' => $request->sent_via,
            'status' => 'Open',
        ]);

        return response()->json($entry);
    }
}