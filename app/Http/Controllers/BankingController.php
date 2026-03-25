<?php

namespace App\Http\Controllers;

use App\Models\Banking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankingController extends Controller
{
    private function canApproveCorporate(): bool
    {
        return Auth::check() && Auth::user()->hasPermission('approve_corporate');
    }

    private function canEditRecord(Banking $record): bool
    {
        if ($this->canApproveCorporate()) {
            return true;
        }

        return (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true);
    }

    private function transformRecord(Banking $record): array
    {
        return [
            'id' => $record->id,
            'date_uploaded' => optional($record->date_uploaded)->format('Y-m-d'),
            'user' => $record->user,
            'submitted_by' => $record->submitted_by,
            'client' => $record->client,
            'tin' => $record->tin,
            'bank' => $record->bank,
            'bank_doc' => $record->bank_doc,
            'status' => $record->status ?? 'Active',
            'workflow_status' => $record->workflow_status ?? 'Uploaded',
            'approval_status' => $record->approval_status ?? 'Pending',
            'approved_by' => $record->approved_by,
            'approved_at' => optional($record->approved_at)->format('Y-m-d H:i:s'),
            'review_note' => $record->review_note,
            'document_name' => $record->document_name,
            'document_path' => $record->document_path,
            'can_edit' => $this->canEditRecord($record),
            'can_submit' => (
                (int) $record->submitted_by === (int) Auth::id()
                && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true)
            ),
        ];
    }

    public function index(Request $request)
    {
        $workflowStatus = $request->get('workflow_status');

        $query = Banking::query();

        if (!$this->canApproveCorporate()) {
            $query->where('submitted_by', Auth::id());
        }

        if ($workflowStatus && $workflowStatus !== 'all') {
            $query->where('workflow_status', ucfirst($workflowStatus));
        }

        $data = $query->orderByDesc('date_uploaded')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($row) => $this->transformRecord($row))
            ->values();

        return response()->json($data);
    }

    public function show($id)
    {
        $record = Banking::findOrFail($id);

        if (!$this->canApproveCorporate() && (int) $record->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json($this->transformRecord($record));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client' => 'required|string|max:255',
            'tin' => 'nullable|string|max:255',
            'bank' => 'required|string|max:255',
            'bank_doc' => 'required|string|max:255',
            'date_uploaded' => 'required|date',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('document');
        $path = $file->store('banking_documents', 'public');

        $isApprover = $this->canApproveCorporate();

        $entry = Banking::create([
            'date_uploaded' => $request->date_uploaded,
            'user' => Auth::user()->name ?? 'Unknown User',
            'submitted_by' => Auth::id(),
            'client' => $request->client,
            'tin' => $request->tin,
            'bank' => $request->bank,
            'bank_doc' => $request->bank_doc,
            'status' => 'Active',
            'workflow_status' => $isApprover ? 'Accepted' : 'Uploaded',
            'approval_status' => $isApprover ? 'Approved' : 'Pending',
            'approved_by' => $isApprover ? Auth::id() : null,
            'approved_at' => $isApprover ? now() : null,
            'review_note' => null,
            'document_name' => $file->getClientOriginalName(),
            'document_path' => 'storage/' . $path,
        ]);

        return response()->json([
            'message' => $isApprover
                ? 'Banking entry saved successfully.'
                : 'Banking entry saved as uploaded record.',
            'data' => $this->transformRecord($entry),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = Banking::findOrFail($id);

        if (!$this->canEditRecord($record)) {
            abort(403, 'This record can no longer be edited.');
        }

        $request->validate([
            'client' => 'required|string|max:255',
            'tin' => 'nullable|string|max:255',
            'bank' => 'required|string|max:255',
            'bank_doc' => 'required|string|max:255',
            'date_uploaded' => 'required|date',
        ]);

        $payload = [
            'client' => $request->client,
            'tin' => $request->tin,
            'bank' => $request->bank,
            'bank_doc' => $request->bank_doc,
            'date_uploaded' => $request->date_uploaded,
        ];

        if ($request->hasFile('document')) {
            $request->validate([
                'document' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]);

            $file = $request->file('document');
            $path = $file->store('banking_documents', 'public');

            $payload['document_name'] = $file->getClientOriginalName();
            $payload['document_path'] = 'storage/' . $path;
        }

        if (($record->workflow_status ?? 'Uploaded') === 'Reverted') {
            $payload['approval_status'] = 'Pending';
            $payload['review_note'] = null;
        }

        $record->update($payload);

        return response()->json([
            'message' => 'Banking entry updated successfully.',
            'data' => $this->transformRecord($record->fresh()),
        ]);
    }

    public function submit($id)
    {
        $record = Banking::findOrFail($id);

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
            'message' => 'Banking entry submitted for approval successfully.',
            'data' => $this->transformRecord($record->fresh()),
        ]);
    }
}