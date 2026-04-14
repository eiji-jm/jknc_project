<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingController extends Controller
{
    private function canApproveCorporate(): bool
    {
        return Auth::check() && Auth::user()->hasPermission('approve_corporate');
    }

    private function canEditRecord(Accounting $record): bool
    {
        if ($this->canApproveCorporate()) {
            return true;
        }

        return (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true);
    }

    private function transformRecord(Accounting $record): array
    {
        return [
            'id' => $record->id,
            'statement_type' => $record->statement_type,
            'client' => $record->client,
            'tin' => $record->tin,
            'date' => optional($record->date)->format('Y-m-d'),
            'user' => $record->user,
            'submitted_by' => $record->submitted_by,
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

    public function page()
    {
        return view('corporate.accounting');
    }

    public function index(Request $request)
    {
        $statementType = $request->get('statement_type');
        $workflowStatus = $request->get('workflow_status');

        $query = Accounting::query();

        if (!$this->canApproveCorporate()) {
            $query->where('submitted_by', Auth::id());
        }

        if ($statementType && $statementType !== 'All Statement Types') {
            $query->where('statement_type', $statementType);
        }

        if ($workflowStatus && $workflowStatus !== 'all') {
            $query->where('workflow_status', ucfirst($workflowStatus));
        }

        $data = $query->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($row) => $this->transformRecord($row))
            ->values();

        return response()->json($data);
    }

    public function show($id)
    {
        $record = Accounting::findOrFail($id);

        if (!$this->canApproveCorporate() && (int) $record->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json($this->transformRecord($record));
    }

    public function store(Request $request)
    {
        $request->validate([
            'statement_type' => 'required|in:PNL,Balance Sheet,Cash Flow,Income Statement,AFS',
            'client' => 'required|string|max:255',
            'tin' => 'nullable|string|max:255',
            'date' => 'required|date',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('document');
        $path = $file->store('accounting_documents', 'public');

        $isApprover = $this->canApproveCorporate();

        $entry = Accounting::create([
            'statement_type' => $request->statement_type,
            'client' => $request->client,
            'tin' => $request->tin,
            'date' => $request->date,
            'user' => Auth::check() ? Auth::user()->name : 'Unknown User',
            'submitted_by' => Auth::id(),
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
                ? 'Accounting entry saved successfully.'
                : 'Accounting entry saved as uploaded record.',
            'data' => $this->transformRecord($entry),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = Accounting::findOrFail($id);

        if (!$this->canEditRecord($record)) {
            abort(403, 'This record can no longer be edited.');
        }

        $request->validate([
            'statement_type' => 'required|in:PNL,Balance Sheet,Cash Flow,Income Statement,AFS',
            'client' => 'required|string|max:255',
            'tin' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $payload = [
            'statement_type' => $request->statement_type,
            'client' => $request->client,
            'tin' => $request->tin,
            'date' => $request->date,
        ];

        if ($request->hasFile('document')) {
            $request->validate([
                'document' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]);

            $file = $request->file('document');
            $path = $file->store('accounting_documents', 'public');

            $payload['document_name'] = $file->getClientOriginalName();
            $payload['document_path'] = 'storage/' . $path;
        }

        if (($record->workflow_status ?? 'Uploaded') === 'Reverted') {
            $payload['approval_status'] = 'Pending';
            $payload['review_note'] = null;
        }

        $record->update($payload);

        return response()->json([
            'message' => 'Accounting entry updated successfully.',
            'data' => $this->transformRecord($record->fresh()),
        ]);
    }

    public function submit($id)
    {
        $record = Accounting::findOrFail($id);

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
            'message' => 'Accounting entry submitted for approval successfully.',
            'data' => $this->transformRecord($record->fresh()),
        ]);
    }
}
