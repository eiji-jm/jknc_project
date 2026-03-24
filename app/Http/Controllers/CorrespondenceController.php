<?php

namespace App\Http\Controllers;

use App\Models\Correspondence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
                    'sender_type' => $item->sender_type,
                    'sender' => $item->sender,
                    'department' => $item->department,
                    'details' => $item->details,
                    'date' => $item->date?->format('Y-m-d'),
                    'time' => $item->time ? Carbon::parse($item->time)->format('H:i') : null,
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
            'sender_type' => 'required|in:From,To',
            'sender' => 'required|string',
            'department' => 'nullable|string',
            'details' => 'nullable|string',
            'deadline' => 'nullable|date',
            'sent_via' => 'nullable|string',
        ]);

        $entry = Correspondence::create([
            'type' => $validated['type'],
            'uploaded_date' => now()->toDateString(),
            'user' => Auth::check() ? Auth::user()->name : 'System',
            'tin' => $validated['tin'] ?? null,
            'subject' => $validated['subject'],
            'sender_type' => $validated['sender_type'],
            'sender' => $validated['sender'],
            'department' => $validated['department'] ?? null,
            'details' => $validated['details'] ?? null,
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
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
            'sender_type' => $entry->sender_type,
            'sender' => $entry->sender,
            'department' => $entry->department,
            'details' => $entry->details,
            'date' => $entry->date?->format('Y-m-d'),
            'time' => $entry->time ? Carbon::parse($entry->time)->format('H:i') : null,
            'deadline' => $entry->deadline?->format('Y-m-d'),
            'sent_via' => $entry->sent_via,
            'status' => $entry->computed_status,
        ], 201);
    }

    protected function resolveTemplateView(string $slug): string
    {
        return match ($slug) {
            'letters' => 'correspondence.templates.letters',
            'demand-letter' => 'correspondence.templates.demand-letter',
            'request-letter' => 'correspondence.templates.request-letter',
            'follow-up-letter' => 'correspondence.templates.follow-up-letter',
            'memo' => 'correspondence.templates.memo',
            'notice' => 'correspondence.templates.notice',
            default => abort(404),
        };
    }

    protected function buildDraftCorrespondence(Request $request, string $slug): object
    {
        $typeMap = [
            'letters' => 'Letters',
            'demand-letter' => 'Demand Letter',
            'request-letter' => 'Request Letter',
            'follow-up-letter' => 'Follow Up Letter',
            'memo' => 'Memo',
            'notice' => 'Notice',
        ];

        return (object) [
            'id' => null,
            'type' => $typeMap[$slug] ?? 'Letters',
            'uploaded_date' => now(),
            'user' => Auth::check() ? Auth::user()->name : 'System',
            'tin' => $request->query('tin'),
            'subject' => $request->query('subject'),
            'sender_type' => $request->query('sender_type', 'From'),
            'sender' => $request->query('sender'),
            'department' => $request->query('department'),
            'details' => $request->query('details'),
            'date' => now(),
            'time' => now(),
            'deadline' => $request->filled('deadline') ? Carbon::parse($request->query('deadline')) : null,
            'sent_via' => $request->query('sent_via', 'Email'),
        ];
    }

    public function showDraftPreview(Request $request, string $slug)
    {
        $correspondence = $this->buildDraftCorrespondence($request, $slug);
        $view = $this->resolveTemplateView($slug);

        return view($view, compact('correspondence'));
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