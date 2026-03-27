<?php

namespace App\Http\Controllers;

use App\Models\Correspondence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CorrespondenceController extends Controller
{
    public function page()
    {
        return view('corporate.correspondence');
    }

    private function canApproveCorporate(): bool
    {
        return Auth::check() && Auth::user()->hasPermission('approve_corporate');
    }

    private function canEditRecord(Correspondence $record): bool
    {
        if ($this->canApproveCorporate()) {
            return true;
        }

        return (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true);
    }

    private function transformRecord(Correspondence $item): array
    {
        return [
            'id' => $item->id,
            'type' => $item->type,
            'uploaded_date' => $item->uploaded_date?->format('Y-m-d'),
            'user' => $item->user,
            'submitted_by' => $item->submitted_by,
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
            'workflow_status' => $item->workflow_status ?? 'Uploaded',
            'approval_status' => $item->approval_status ?? 'Pending',
            'approved_by' => $item->approved_by,
            'approved_at' => optional($item->approved_at)->format('Y-m-d H:i:s'),
            'review_note' => $item->review_note,
            'can_edit' => $this->canEditRecord($item),
            'can_submit' => (
                (int) $item->submitted_by === (int) Auth::id()
                && in_array($item->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true)
            ),
        ];
    }

    public function index(Request $request)
    {
        $query = Correspondence::query();

        if (!$this->canApproveCorporate()) {
            $query->where('submitted_by', Auth::id());
        }

        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('type', $request->type);
        }

        if ($request->filled('workflow_status') && $request->workflow_status !== 'all') {
            $query->where('workflow_status', ucfirst($request->workflow_status));
        }

        $data = $query
            ->orderBy('uploaded_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn ($item) => $this->transformRecord($item))
            ->values();

        return response()->json($data);
    }

    public function show($id)
    {
        $record = Correspondence::findOrFail($id);

        if (!$this->canApproveCorporate() && (int) $record->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json($this->transformRecord($record));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:Letters,Demand Letter,Request Letter,Follow Up Letter,Memo,Notice',
            'tin' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'sender_type' => 'required|in:From,To',
            'sender' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'details' => 'nullable|string',
            'deadline' => 'nullable|date',
            'sent_via' => 'nullable|string|max:255',
        ]);

        $isApprover = $this->canApproveCorporate();

        $entry = Correspondence::create([
            'type' => $validated['type'],
            'uploaded_date' => now()->toDateString(),
            'user' => Auth::check() ? Auth::user()->name : 'System',
            'submitted_by' => Auth::id(),
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
            'workflow_status' => $isApprover ? 'Accepted' : 'Uploaded',
            'approval_status' => $isApprover ? 'Approved' : 'Pending',
            'approved_by' => $isApprover ? Auth::id() : null,
            'approved_at' => $isApprover ? now() : null,
            'review_note' => null,
        ]);

        return response()->json([
            'success' => true,
            'id' => $entry->id,
            'message' => $isApprover
                ? 'Correspondence saved successfully.'
                : 'Correspondence saved as uploaded record.',
            'data' => $this->transformRecord($entry),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = Correspondence::findOrFail($id);

        if (!$this->canEditRecord($record)) {
            abort(403, 'This record can no longer be edited.');
        }

        $validated = $request->validate([
            'type' => 'required|in:Letters,Demand Letter,Request Letter,Follow Up Letter,Memo,Notice',
            'tin' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'sender_type' => 'required|in:From,To',
            'sender' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'details' => 'nullable|string',
            'deadline' => 'nullable|date',
            'sent_via' => 'nullable|string|max:255',
        ]);

        $payload = [
            'type' => $validated['type'],
            'tin' => $validated['tin'] ?? null,
            'subject' => $validated['subject'],
            'sender_type' => $validated['sender_type'],
            'sender' => $validated['sender'],
            'department' => $validated['department'] ?? null,
            'details' => $validated['details'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'sent_via' => $validated['sent_via'] ?? 'Email',
        ];

        if (($record->workflow_status ?? 'Uploaded') === 'Reverted') {
            $payload['approval_status'] = 'Pending';
            $payload['review_note'] = null;
        }

        $record->update($payload);

        return response()->json([
            'message' => 'Correspondence updated successfully.',
            'data' => $this->transformRecord($record->fresh()),
        ]);
    }

    public function submit($id)
    {
        $record = Correspondence::findOrFail($id);

        if ((int) $record->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true)) {
            return response()->json([
                'message' => 'Only uploaded or reverted records can be submitted.'
            ], 422);
        }

        $record->update([
            'workflow_status' => 'Submitted',
            'approval_status' => 'Pending',
            'review_note' => null,
        ]);

        return response()->json([
            'message' => 'Correspondence submitted for approval successfully.',
            'data' => $this->transformRecord($record->fresh()),
        ]);
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