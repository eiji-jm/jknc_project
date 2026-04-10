<?php

namespace App\Http\Controllers;

use App\Models\FinanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    private const MODULES = [
        'supplier' => 'Supplier',
        'service' => 'Service',
        'product' => 'Product',
        'chart_account' => 'Chart of Accounts',
        'bank_account' => 'Bank Accounts',
        'pr' => 'Purchase Request',
        'po' => 'Purchase Order',
        'ca' => 'Cash Advance',
        'lr' => 'Liquidation Report',
        'err' => 'Expense Reimbursement Request',
        'dv' => 'Disbursement Voucher',
        'pda' => 'Payroll Disbursement Authorization',
        'crf' => 'Cash Return Form',
        'ibtf' => 'Interbank Fund Transfer Form',
        'arf' => 'Asset Registration Form',
    ];

    private const WORKFLOW_STATUSES = [
        'Uploaded',
        'Submitted',
        'Shared',
        'Accepted',
        'Reverted',
        'Archived',
    ];

    private function canApproveFinance(): bool
    {
        return Auth::check() && Auth::user()->hasPermission('approve_corporate');
    }

    private function moduleKeys(): array
    {
        return array_keys(self::MODULES);
    }

    private function moduleLabel(string $moduleKey): string
    {
        return self::MODULES[$moduleKey] ?? Str::headline($moduleKey);
    }

    private function acceptedRecordQuery(string $moduleKey, array $dataConstraints = [])
    {
        $query = FinanceRecord::query()
            ->where('module_key', $moduleKey)
            ->where(function ($statusQuery) {
                $statusQuery->where('workflow_status', 'Accepted')
                    ->orWhere('approval_status', 'Approved');
            });

        foreach ($dataConstraints as $field => $value) {
            $query->where("data->{$field}", $value);
        }

        return $query;
    }

    private function canEditRecord(FinanceRecord $record): bool
    {
        if ($this->canApproveFinance()) {
            return true;
        }

        return (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true);
    }

    private function canSubmitRecord(FinanceRecord $record): bool
    {
        return (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true);
    }

    private function canShareSupplierRecord(FinanceRecord $record): bool
    {
        return $record->module_key === 'supplier'
            && (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true)
            && data_get($record->data, 'completion_mode') === 'send_to_supplier';
    }

    private function optionLabel(FinanceRecord $record): string
    {
        $parts = array_filter([
            $record->record_number,
            $record->record_title,
        ]);

        if ($parts) {
            return implode(' - ', $parts);
        }

        $dataTitle = data_get($record->data, 'title')
            ?: data_get($record->data, 'business_name')
            ?: data_get($record->data, 'account_name')
            ?: data_get($record->data, 'service_name')
            ?: data_get($record->data, 'product_name')
            ?: data_get($record->data, 'payee')
            ?: data_get($record->data, 'supplier_name');

        if ($dataTitle) {
            return (string) $dataTitle;
        }

        return $this->moduleLabel($record->module_key) . ' #' . $record->id;
    }

    private function resolveLookupOptions(): array
    {
        $options = [];

        foreach (self::MODULES as $moduleKey => $label) {
            $options[$moduleKey] = $this->acceptedRecordQuery($moduleKey)
                ->orderByDesc('record_date')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn (FinanceRecord $record) => [
                    'id' => $record->id,
                    'label' => $this->optionLabel($record),
                    'record_number' => $record->record_number,
                    'record_title' => $record->record_title,
                ])
                ->values();
        }

        $options['dv_ca'] = $this->acceptedRecordQuery('dv', ['source_document_type' => 'ca'])
            ->orderByDesc('record_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (FinanceRecord $record) => [
                'id' => $record->id,
                'label' => $this->optionLabel($record),
                'record_number' => $record->record_number,
                'record_title' => $record->record_title,
            ])
            ->values();

        $options['lr_overage'] = $this->acceptedRecordQuery('lr', ['variance_indicator' => 'Overage'])
            ->orderByDesc('record_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (FinanceRecord $record) => [
                'id' => $record->id,
                'label' => $this->optionLabel($record),
                'record_number' => $record->record_number,
                'record_title' => $record->record_title,
            ])
            ->values();

        return $options;
    }

    private function transformRecord(FinanceRecord $record): array
    {
        return [
            'id' => $record->id,
            'module_key' => $record->module_key,
            'module_label' => $this->moduleLabel($record->module_key),
            'record_number' => $record->record_number,
            'record_title' => $record->record_title,
            'display_label' => $this->optionLabel($record),
            'record_date' => optional($record->record_date)->format('Y-m-d'),
            'amount' => $record->amount,
            'status' => $record->status ?? 'Active',
            'workflow_status' => $record->workflow_status ?? 'Uploaded',
            'approval_status' => $record->approval_status ?? 'Pending',
            'submitted_by' => $record->submitted_by,
            'submitted_at' => optional($record->submitted_at)->format('Y-m-d H:i:s'),
            'approved_by' => $record->approved_by,
            'approved_at' => optional($record->approved_at)->format('Y-m-d H:i:s'),
            'review_note' => $record->review_note,
            'data' => $record->data ?? [],
            'attachments' => $record->attachments ?? [],
            'share_token' => $record->share_token,
            'shared_at' => optional($record->shared_at)->format('Y-m-d H:i:s'),
            'supplier_completed_at' => optional($record->supplier_completed_at)->format('Y-m-d H:i:s'),
            'user' => $record->user,
            'can_edit' => $this->canEditRecord($record),
            'can_submit' => $this->canSubmitRecord($record),
            'can_share_supplier' => $this->canShareSupplierRecord($record),
            'can_review' => $this->canApproveFinance(),
            'supplier_completion_url' => $record->share_token
                ? route('finance.supplier.completion', $record->share_token)
                : null,
        ];
    }

    private function persistAttachments(Request $request, array $existingAttachments = []): array
    {
        $attachments = $existingAttachments;

        if (!$request->hasFile('attachments')) {
            return $attachments;
        }

        foreach ((array) $request->file('attachments') as $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store('finance_documents', 'public');

            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => 'storage/' . $path,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ];
        }

        return $attachments;
    }

    private function commonValidationRules(): array
    {
        return [
            'module_key' => 'required|in:' . implode(',', $this->moduleKeys()),
            'record_number' => 'required|string|max:255',
            'record_title' => 'required|string|max:255',
            'record_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'data' => 'nullable|array',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ];
    }

    private function acceptedLinkedRecordRule(string $moduleKey, array $dataConstraints = [])
    {
        return Rule::exists('finance_records', 'id')->where(function ($query) use ($moduleKey, $dataConstraints) {
            $query->where('module_key', $moduleKey)
                ->where(function ($statusQuery) {
                    $statusQuery->where('workflow_status', 'Accepted')
                        ->orWhere('approval_status', 'Approved');
                });

            foreach ($dataConstraints as $field => $value) {
                $query->where("data->{$field}", $value);
            }
        });
    }

    private function moduleSpecificRules(string $moduleKey): array
    {
        $rules = [
            'supplier' => [
                'data.completion_mode' => 'nullable|in:complete_internally,send_to_supplier',
            ],
            'service' => [
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'product' => [
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'chart_account' => [
                'data.is_sub_account' => 'nullable|boolean',
                'data.parent_account_id' => ['nullable', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'bank_account' => [
                'data.linked_coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'pr' => [
                'data.requesting_department' => 'required|string|max:255',
                'data.requestor' => 'required|string|max:255',
                'data.request_type' => 'required|in:Service,Product',
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.master_item_type' => 'required|in:service,product',
                'data.master_item_id' => 'required|integer',
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
                'data.quantity' => 'nullable|numeric|min:0',
                'data.unit_cost' => 'nullable|numeric|min:0',
                'data.estimated_total_cost' => 'nullable|numeric|min:0',
            ],
            'po' => [
                'data.linked_pr_id' => ['required', $this->acceptedLinkedRecordRule('pr')],
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.linked_item_type' => 'required|in:service,product',
                'data.linked_item_id' => 'required|integer',
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
                'data.quantity' => 'nullable|numeric|min:0',
                'data.unit_cost' => 'nullable|numeric|min:0',
                'data.total_amount' => 'nullable|numeric|min:0',
            ],
            'ca' => [
                'data.requestor' => 'required|string|max:255',
                'data.purpose' => 'required|string|max:2000',
                'data.amount_requested' => 'required|numeric|min:0',
                'data.mode_of_release' => 'required|in:Cash,Bank Transfer,Check',
                'data.bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'lr' => [
                'data.linked_dv_id' => ['required', $this->acceptedLinkedRecordRule('dv', ['source_document_type' => 'ca'])],
                'data.linked_ca_id' => ['nullable', $this->acceptedLinkedRecordRule('ca')],
                'data.total_cash_advance' => 'required|numeric|min:0',
                'data.actual_expenses' => 'required|numeric|min:0',
                'data.variance' => 'required|numeric',
                'data.variance_indicator' => 'required|in:Shortage,Overage,Balanced',
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'err' => [
                'data.linked_lr_id' => ['required', $this->acceptedLinkedRecordRule('lr')],
                'data.amount' => 'required|numeric|min:0',
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
                'data.bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.supplier_id' => ['nullable', $this->acceptedLinkedRecordRule('supplier')],
            ],
            'dv' => [
                'data.source_document_type' => 'required|in:pr,po,ca,lr,err,pda,crf,ibtf,arf',
                'data.source_document_id' => 'required',
                'data.amount' => 'required|numeric|min:0',
                'data.payment_type' => 'required|in:Cash,Check,Bank Transfer,E-Wallet',
                'data.bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
                'data.supplier_id' => ['nullable', $this->acceptedLinkedRecordRule('supplier')],
            ],
            'pda' => [
                'data.total_payroll_amount' => 'required|numeric|min:0',
                'data.funding_bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.payroll_expense_coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'crf' => [
                'data.linked_lr_id' => ['required', $this->acceptedLinkedRecordRule('lr', ['variance_indicator' => 'Overage'])],
                'data.amount_returned' => 'required|numeric|min:0',
                'data.receiving_bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'ibtf' => [
                'data.source_bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.destination_bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.amount' => 'required|numeric|min:0',
                'data.reason' => 'required|string|max:2000',
            ],
            'arf' => [
                'data.linked_po_id' => ['nullable', $this->acceptedLinkedRecordRule('po')],
                'data.linked_dv_id' => ['nullable', $this->acceptedLinkedRecordRule('dv')],
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.acquisition_cost' => 'required|numeric|min:0',
                'data.acquisition_date' => 'required|date',
                'data.asset_coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
        ];

        if ($moduleKey === 'po') {
            $rules['data.linked_item_id'] = ['required', $this->acceptedLinkedRecordRule('service')];
        }

        if ($moduleKey === 'pr') {
            $rules['data.master_item_id'] = ['required', $this->acceptedLinkedRecordRule('service')];
        }

        if ($moduleKey === 'chart_account') {
            $rules['data.parent_account_id'] = ['nullable', $this->acceptedLinkedRecordRule('chart_account')];
        }

        if ($moduleKey === 'dv') {
            $rules['data.source_document_id'] = [
                'required',
                'integer',
            ];
        }

        if ($moduleKey === 'lr') {
            $rules['data.linked_ca_id'] = ['nullable', $this->acceptedLinkedRecordRule('ca')];
        }

        if ($moduleKey === 'arf') {
            $rules['data.linked_po_id'] = ['nullable', $this->acceptedLinkedRecordRule('po')];
            $rules['data.linked_dv_id'] = ['nullable', $this->acceptedLinkedRecordRule('dv')];
            $rules['data.linked_po_id'][] = 'required_without:data.linked_dv_id';
            $rules['data.linked_dv_id'][] = 'required_without:data.linked_po_id';
        }

        return $rules[$moduleKey] ?? [];
    }

    private function validateModulePayload(Request $request, ?FinanceRecord $financeRecord = null): void
    {
        $moduleKey = (string) $request->input('module_key', $financeRecord?->module_key);
        $rules = array_merge($this->commonValidationRules(), $this->moduleSpecificRules($moduleKey));

        if ($financeRecord) {
            $rules['module_key'] = ['required', Rule::in([$financeRecord->module_key])];
        }

        $validated = $request->validate($rules);

        if ($moduleKey === 'chart_account' && !blank(data_get($validated, 'data.is_sub_account')) && blank(data_get($validated, 'data.parent_account_id'))) {
            throw ValidationException::withMessages([
                'data.parent_account_id' => 'Main Account is required when Sub-Account is enabled.',
            ]);
        }

        if ($moduleKey === 'pr') {
            $itemType = data_get($validated, 'data.master_item_type');
            $itemId = data_get($validated, 'data.master_item_id');

            $itemModule = $itemType === 'product' ? 'product' : 'service';
            if (!$this->recordExistsForWorkflow($itemModule, $itemId)) {
                throw ValidationException::withMessages([
                    'data.master_item_id' => 'The selected item must exist in the chosen master list and be approved.',
                ]);
            }
        }

        if ($moduleKey === 'po') {
            $itemType = data_get($validated, 'data.linked_item_type');
            $itemId = data_get($validated, 'data.linked_item_id');

            $itemModule = $itemType === 'product' ? 'product' : 'service';
            if (!$this->recordExistsForWorkflow($itemModule, $itemId)) {
                throw ValidationException::withMessages([
                    'data.linked_item_id' => 'The selected item must exist in the chosen master list and be approved.',
                ]);
            }
        }

        if ($moduleKey === 'dv') {
            $sourceDocumentType = data_get($validated, 'data.source_document_type');
            $sourceDocumentId = data_get($validated, 'data.source_document_id');

            $allowedModule = match ($sourceDocumentType) {
                'pr' => 'pr',
                'po' => 'po',
                'ca' => 'ca',
                'lr' => 'lr',
                'err' => 'err',
                'pda' => 'pda',
                'crf' => 'crf',
                'ibtf' => 'ibtf',
                'arf' => 'arf',
                default => null,
            };

            if (!$allowedModule || !$this->recordExistsForWorkflow($allowedModule, $sourceDocumentId)) {
                throw ValidationException::withMessages([
                    'data.source_document_id' => 'The linked source document is invalid or is not yet approved.',
                ]);
            }
        }

        if ($moduleKey === 'lr') {
            $linkedDvId = data_get($validated, 'data.linked_dv_id');

            if (!$this->recordExistsForWorkflow('dv', $linkedDvId, ['source_document_type' => 'ca'])) {
                throw ValidationException::withMessages([
                    'data.linked_dv_id' => 'The selected DV must come from a cash advance.',
                ]);
            }

            $linkedCaId = data_get($validated, 'data.linked_ca_id');
            if (!blank($linkedCaId)) {
                $linkedDv = FinanceRecord::query()->find($linkedDvId);
                $linkedCaFromDv = data_get($linkedDv?->data, 'source_document_id');

                if ((string) $linkedCaFromDv !== (string) $linkedCaId) {
                    throw ValidationException::withMessages([
                        'data.linked_ca_id' => 'The selected CA must match the CA used by the linked DV.',
                    ]);
                }
            }
        }

        if ($moduleKey === 'crf') {
            $linkedLrId = data_get($validated, 'data.linked_lr_id');

            if (!$this->recordExistsForWorkflow('lr', $linkedLrId, ['variance_indicator' => 'Overage'])) {
                throw ValidationException::withMessages([
                    'data.linked_lr_id' => 'The selected LR must be approved and marked as overage.',
                ]);
            }
        }

        if ($moduleKey === 'arf') {
            $linkedPoId = data_get($validated, 'data.linked_po_id');
            $linkedDvId = data_get($validated, 'data.linked_dv_id');

            if (blank($linkedPoId) && blank($linkedDvId)) {
                throw ValidationException::withMessages([
                    'data.linked_po_id' => 'ARF must be linked to either a PO or a DV.',
                    'data.linked_dv_id' => 'ARF must be linked to either a PO or a DV.',
                ]);
            }
        }

        if ($moduleKey === 'ibtf' && data_get($validated, 'data.source_bank_account_id') === data_get($validated, 'data.destination_bank_account_id')) {
            throw ValidationException::withMessages([
                'data.destination_bank_account_id' => 'Source and destination bank accounts must be different.',
            ]);
        }
    }

    private function normalizeModuleData(string $moduleKey, array $data): array
    {
        if ($moduleKey === 'supplier' && blank(data_get($data, 'completion_mode'))) {
            data_set($data, 'completion_mode', 'complete_internally');
        }

        return $data;
    }

    private function recordExistsForWorkflow(string $moduleKey, mixed $recordId, array $dataConstraints = []): bool
    {
        return $this->acceptedRecordQuery($moduleKey, $dataConstraints)
            ->where('id', $recordId)
            ->exists();
    }

    public function index(Request $request)
    {
        $moduleKey = $request->get('module', 'supplier');
        $workflowFilter = $request->get('workflow_status', 'all');

        if (!array_key_exists($moduleKey, self::MODULES)) {
            $moduleKey = 'supplier';
        }

        if (!in_array($workflowFilter, array_merge(['all'], self::WORKFLOW_STATUSES), true)) {
            $workflowFilter = 'all';
        }

        $query = FinanceRecord::query();

        if (!$this->canApproveFinance()) {
            $query->where('submitted_by', Auth::id());
        }

        $records = $query->orderByDesc('record_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (FinanceRecord $record) => $this->transformRecord($record))
            ->values();

        return view('finance.index', [
            'records' => $records,
            'moduleLabels' => self::MODULES,
            'lookupOptions' => $this->resolveLookupOptions(),
            'currentModule' => $moduleKey,
            'currentWorkflowFilter' => $workflowFilter,
            'canApproveFinance' => $this->canApproveFinance(),
            'currentUserName' => Auth::user()->name ?? 'Unknown User',
        ]);
    }

    public function show(FinanceRecord $financeRecord)
    {
        if (!$this->canApproveFinance() && (int) $financeRecord->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json($this->transformRecord($financeRecord));
    }

    public function store(Request $request)
    {
        $this->validateModulePayload($request);

        $isApprover = $this->canApproveFinance();
        $attachments = $this->persistAttachments($request);
        $data = $this->normalizeModuleData($request->module_key, $request->input('data', []));

        $record = FinanceRecord::create([
            'module_key' => $request->module_key,
            'record_number' => $request->record_number,
            'record_title' => $request->record_title,
            'record_date' => $request->record_date,
            'amount' => $request->amount,
            'status' => $request->status,
            'workflow_status' => $isApprover ? 'Accepted' : 'Uploaded',
            'approval_status' => $isApprover ? 'Approved' : 'Pending',
            'submitted_by' => Auth::id(),
            'submitted_at' => $isApprover ? now() : null,
            'approved_by' => $isApprover ? Auth::id() : null,
            'approved_at' => $isApprover ? now() : null,
            'review_note' => null,
            'data' => $data,
            'attachments' => $attachments,
            'share_token' => null,
            'shared_at' => null,
            'user' => Auth::user()->name ?? 'Unknown User',
        ]);

        return response()->json([
            'message' => $record->workflow_status === 'Shared'
                ? 'Finance record created and shared with supplier.'
                : ($isApprover ? 'Finance record saved and accepted.' : 'Finance record saved successfully.'),
            'data' => $this->transformRecord($record),
        ], 201);
    }

    public function update(Request $request, FinanceRecord $financeRecord)
    {
        if (!$this->canEditRecord($financeRecord)) {
            abort(403, 'This record can no longer be edited.');
        }

        $this->validateModulePayload($request, $financeRecord);

        $existingAttachments = json_decode((string) $request->input('existing_attachments_json', '[]'), true);
        $existingAttachments = is_array($existingAttachments) ? $existingAttachments : [];
        $attachments = $this->persistAttachments($request, $existingAttachments);
        $data = $this->normalizeModuleData($request->module_key, $request->input('data', []));

        $payload = [
            'module_key' => $request->module_key,
            'record_number' => $request->record_number,
            'record_title' => $request->record_title,
            'record_date' => $request->record_date,
            'amount' => $request->amount,
            'status' => $request->status,
            'data' => $data,
            'attachments' => $attachments,
        ];

        if (($financeRecord->workflow_status ?? 'Uploaded') === 'Reverted') {
            $payload['approval_status'] = 'Pending';
            $payload['review_note'] = null;
        }

        $financeRecord->update($payload);

        return response()->json([
            'message' => 'Finance record updated successfully.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function submit(FinanceRecord $financeRecord)
    {
        if ((int) $financeRecord->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($financeRecord->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true)) {
            return response()->json([
                'message' => 'Only uploaded or reverted records can be submitted.'
            ], 422);
        }

        $financeRecord->update([
            'workflow_status' => 'Submitted',
            'approval_status' => 'Pending',
            'submitted_at' => now(),
            'review_note' => null,
        ]);

        return response()->json([
            'message' => 'Finance record submitted for review successfully.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function approve(FinanceRecord $financeRecord)
    {
        if (!$this->canApproveFinance()) {
            abort(403, 'Unauthorized');
        }

        $financeRecord->update([
            'workflow_status' => 'Accepted',
            'approval_status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Finance record approved successfully.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function revert(Request $request, FinanceRecord $financeRecord)
    {
        if (!$this->canApproveFinance()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'review_note' => 'required|string|max:1000',
        ]);

        $financeRecord->update([
            'workflow_status' => 'Reverted',
            'approval_status' => 'Needs Revision',
            'review_note' => $request->review_note,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Finance record reverted for revision.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function archive(FinanceRecord $financeRecord)
    {
        if (!$this->canApproveFinance()) {
            abort(403, 'Unauthorized');
        }

        $financeRecord->update([
            'workflow_status' => 'Archived',
            'approval_status' => 'Archived',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Finance record archived successfully.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function shareSupplierLink(FinanceRecord $financeRecord)
    {
        if (!$this->canShareSupplierRecord($financeRecord)) {
            abort(403, 'Unauthorized');
        }

        if (blank($financeRecord->share_token)) {
            $financeRecord->update([
                'share_token' => Str::random(64),
                'shared_at' => now(),
                'workflow_status' => 'Shared',
                'approval_status' => 'Pending Supplier Completion',
            ]);
        } else {
            $financeRecord->update([
                'shared_at' => now(),
                'workflow_status' => 'Shared',
                'approval_status' => 'Pending Supplier Completion',
            ]);
        }

        return response()->json([
            'message' => 'Supplier completion link is ready.',
            'link' => route('finance.supplier.completion', $financeRecord->fresh()->share_token),
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function supplierCompletionForm(string $token)
    {
        $record = FinanceRecord::query()
            ->where('module_key', 'supplier')
            ->where('share_token', $token)
            ->firstOrFail();

        return view('finance.supplier-completion', [
            'record' => $this->transformRecord($record),
        ]);
    }

    public function submitSupplierCompletion(Request $request, string $token)
    {
        $record = FinanceRecord::query()
            ->where('module_key', 'supplier')
            ->where('share_token', $token)
            ->firstOrFail();

        $request->validate([
            'record_title' => 'required|string|max:255',
            'record_number' => 'required|string|max:255',
            'record_date' => 'required|date',
            'data.representative_full_name' => 'required|string|max:255',
            'data.email_address' => 'required|email|max:255',
            'data.phone_number' => 'required|string|max:255',
            'data.business_address' => 'nullable|string|max:1000',
            'data.billing_address' => 'nullable|string|max:1000',
        ]);

        $data = array_merge($record->data ?? [], $request->input('data', []));
        $data['business_name'] = $request->record_title;

        $record->update([
            'record_number' => $request->record_number,
            'record_title' => $request->record_title,
            'record_date' => $request->record_date,
            'data' => $data,
            'workflow_status' => 'Submitted',
            'approval_status' => 'Pending',
            'supplier_completed_at' => now(),
        ]);

        return redirect()
            ->route('finance.supplier.completion', $token)
            ->with('success', 'Supplier information submitted successfully. It is now ready for internal review.');
    }
}
