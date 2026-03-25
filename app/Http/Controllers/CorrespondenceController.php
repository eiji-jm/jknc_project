<?php

namespace App\Http\Controllers;

use App\Models\Correspondence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CorrespondenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Correspondence::query();

        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('type', $request->type);
        }

        $data = $query
            ->orderBy('uploaded_date', 'desc')
            ->orderBy('id', 'desc')
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
            'type' => 'required|in:Letters,Demand Letter,Request Letter,Follow Up Letter,Memo,Notice',
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
        ]);

        return response()->json([
            'success' => true,
            'id' => $entry->id,
            'message' => 'Correspondence saved successfully.',
            'status' => $entry->computed_status,
        ], 201);
    }

    protected function typeMap(): array
    {
        return [
            'letters' => 'Letters',
            'demand-letter' => 'Demand Letter',
            'request-letter' => 'Request Letter',
            'follow-up-letter' => 'Follow Up Letter',
            'memo' => 'Memo',
            'notice' => 'Notice',
        ];
    }

    protected function resolveTemplateTitle(string $slug): string
    {
        $map = $this->typeMap();

        if (!array_key_exists($slug, $map)) {
            abort(404);
        }

        return $map[$slug];
    }

    protected function buildDraftCorrespondence(Request $request, string $slug): object
    {
        return (object) [
            'id' => null,
            'type' => $this->resolveTemplateTitle($slug),
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
        $title = $this->resolveTemplateTitle($slug);

        return view('correspondence.templates.base-template', compact('correspondence', 'title'));
    }

    public function showTemplate(string $slug, int $id)
    {
        $correspondence = Correspondence::findOrFail($id);
        $title = $this->resolveTemplateTitle($slug);

        return Pdf::loadView('correspondence.templates.base-template', compact('correspondence', 'title'))
            ->setPaper('a4', 'portrait')
            ->stream(strtolower($slug . '-' . $correspondence->id . '.pdf'));
    }
}