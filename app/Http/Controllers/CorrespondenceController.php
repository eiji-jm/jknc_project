<?php

namespace App\Http\Controllers;

use App\Models\Correspondence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CorrespondenceController extends Controller
{
    public function index($type)
    {
        $data = Correspondence::where('type', $type)
            ->orderBy('uploaded_date', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'uploaded_date' => $item->uploaded_date?->format('Y-m-d'),
                    'user' => $item->user,
                    'tin' => $item->tin,
                    'subject' => $item->subject,
                    'from' => $item->from,
                    'to' => $item->to,
                    'department' => $item->department,
                    'details' => $item->details,
                    'date' => $item->date?->format('Y-m-d'),
                    'time' => $item->time ? \Carbon\Carbon::parse($item->time)->format('H:i') : null,
                    'deadline' => $item->deadline?->format('Y-m-d'),
                    'sent_via' => $item->sent_via,
                    'status' => $item->computed_status,
                ];
            });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
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
            'user' => Auth::check() ? Auth::user()->name : 'System',
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

        return response()->json([
            'id' => $entry->id,
            'type' => $entry->type,
            'uploaded_date' => $entry->uploaded_date?->format('Y-m-d'),
            'user' => $entry->user,
            'tin' => $entry->tin,
            'subject' => $entry->subject,
            'from' => $entry->from,
            'to' => $entry->to,
            'department' => $entry->department,
            'details' => $entry->details,
            'date' => $entry->date?->format('Y-m-d'),
            'time' => $entry->time ? \Carbon\Carbon::parse($entry->time)->format('H:i') : null,
            'deadline' => $entry->deadline?->format('Y-m-d'),
            'sent_via' => $entry->sent_via,
            'status' => $entry->computed_status,
        ], 201);
    }

    public function showLettersTemplate($id)
    {
        $correspondence = Correspondence::findOrFail($id);

        $pdf = Pdf::loadView('correspondence.templates.letters', compact('correspondence'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('letters-' . $correspondence->id . '.pdf');
    }

    public function showDemandLetterTemplate($id)
    {
        $correspondence = Correspondence::findOrFail($id);

        $pdf = Pdf::loadView('correspondence.templates.demand-letter', compact('correspondence'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('demand-letter-' . $correspondence->id . '.pdf');
    }

    public function showRequestLetterTemplate($id)
    {
        $correspondence = Correspondence::findOrFail($id);

        $pdf = Pdf::loadView('correspondence.templates.request-letter', compact('correspondence'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('request-letter-' . $correspondence->id . '.pdf');
    }

    public function showFollowUpLetterTemplate($id)
    {
        $correspondence = Correspondence::findOrFail($id);

        $pdf = Pdf::loadView('correspondence.templates.follow-up-letter', compact('correspondence'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('follow-up-letter-' . $correspondence->id . '.pdf');
    }

    public function showMemoTemplate($id)
    {
        $correspondence = Correspondence::findOrFail($id);

        $pdf = Pdf::loadView('correspondence.templates.memo', compact('correspondence'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('memo-' . $correspondence->id . '.pdf');
    }

    public function showNoticeTemplate($id)
    {
        $correspondence = Correspondence::findOrFail($id);

        $pdf = Pdf::loadView('correspondence.templates.notice', compact('correspondence'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('notice-' . $correspondence->id . '.pdf');
    }
}