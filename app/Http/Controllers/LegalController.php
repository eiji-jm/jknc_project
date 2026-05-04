<?php

namespace App\Http\Controllers;

use App\Models\Legal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LegalController extends Controller
{
    private function canApproveCorporate(): bool
    {
        return Auth::check() && Auth::user()->hasPermission('approve_corporate');
    }

    private function canEditRecord(Legal $record): bool
    {
        if ($this->canApproveCorporate()) {
            return true;
        }

        return (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true);
    }

    private function transformRecord(Legal $item): array
    {
        return [
            'id' => $item->id,
            'legal_type' => $item->legal_type,
            'client' => $item->client,
            'tin' => $item->tin,
            'date' => $item->date?->format('Y-m-d'),
            'document_type' => $item->document_type,
            'document_name' => $item->document_name,
            'document_path' => $item->document_path,
            'user' => $item->user,
            'submitted_by' => $item->submitted_by,
            'status' => $item->status,
            'workflow_status' => $item->workflow_status ?? 'Uploaded',
            'approval_status' => $item->approval_status ?? 'Pending',
            'approved_by' => $item->approved_by,
            'approved_at' => optional($item->approved_at)->format('Y-m-d H:i:s'),
            'review_note' => $item->review_note,
            'document_url' => $item->document_path ? asset($item->document_path) : null,
            'can_edit' => $this->canEditRecord($item),
            'can_submit' => (
                (int) $item->submitted_by === (int) Auth::id()
                && in_array($item->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true)
            ),
        ];
    }

    public function index(Request $request)
    {
        $query = Legal::query();

        if (!$this->canApproveCorporate()) {
            $query->where('submitted_by', Auth::id());
        }

        if ($request->filled('type') && $request->type !== 'All Types') {
            $query->where('legal_type', $request->type);
        }

        if ($request->filled('workflow_status') && $request->workflow_status !== 'all') {
            $query->where('workflow_status', ucfirst($request->workflow_status));
        }

        $data = $query
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($item) => $this->transformRecord($item))
            ->values();

        return response()->json($data);
    }

    public function show($id)
    {
        $record = Legal::findOrFail($id);

        if (!$this->canApproveCorporate() && (int) $record->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json($this->transformRecord($record));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'legal_type' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'tin' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'document_type' => 'nullable|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $relativePath = null;
        $documentName = null;

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

            $destinationPath = public_path('documents/legal');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $documentName);
            $relativePath = 'documents/legal/' . $documentName;
        }

        $isApprover = $this->canApproveCorporate();

        $legal = Legal::create([
            'legal_type' => $validated['legal_type'],
            'client' => $validated['client'],
            'tin' => $validated['tin'] ?? null,
            'date' => $validated['date'] ?? now()->toDateString(),
            'document_type' => $validated['document_type'] ?? null,
            'document_name' => $documentName,
            'document_path' => $relativePath,
            'user' => Auth::check() ? Auth::user()->name : 'System',
            'submitted_by' => Auth::id(),
            'workflow_status' => $isApprover ? 'Accepted' : 'Uploaded',
            'approval_status' => $isApprover ? 'Approved' : 'Pending',
            'approved_by' => $isApprover ? Auth::id() : null,
            'approved_at' => $isApprover ? now() : null,
            'review_note' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $isApprover
                ? 'Legal document saved successfully.'
                : 'Legal document saved as uploaded record.',
            'data' => $this->transformRecord($legal),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = Legal::findOrFail($id);

        if (!$this->canEditRecord($record)) {
            abort(403, 'This record can no longer be edited.');
        }

        $validated = $request->validate([
            'legal_type' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'tin' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'document_type' => 'nullable|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $payload = [
            'legal_type' => $validated['legal_type'],
            'client' => $validated['client'],
            'tin' => $validated['tin'] ?? null,
            'date' => $validated['date'] ?? now()->toDateString(),
            'document_type' => $validated['document_type'] ?? null,
        ];

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

            $destinationPath = public_path('documents/legal');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $documentName);

            $payload['document_name'] = $documentName;
            $payload['document_path'] = 'documents/legal/' . $documentName;
        }

        if (($record->workflow_status ?? 'Uploaded') === 'Reverted') {
            $payload['approval_status'] = 'Pending';
            $payload['review_note'] = null;
        }

        $record->update($payload);

        return response()->json([
            'message' => 'Legal document updated successfully.',
            'data' => $this->transformRecord($record->fresh()),
        ]);
    }

    public function submit($id)
    {
        $record = Legal::findOrFail($id);

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
            'message' => 'Legal document submitted for approval successfully.',
            'data' => $this->transformRecord($record->fresh()),
        ]);
    }
}