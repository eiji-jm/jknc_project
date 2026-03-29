<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesPdfPreview;
use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Concerns\SyncsDeadlineTownHallMemo;
use App\Models\GisRecord;
use App\Models\NatGov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class NatGovController extends Controller
{
    use GeneratesPdfPreview;
    use HandlesUploads;
    use SyncsDeadlineTownHallMemo;

    public function index()
    {
        $natgovs = NatGov::latest()->get();

        return view('corporate.natgov.index', [
            'natgovs' => $natgovs,
            'companyDefaults' => $this->companyDefaults(),
        ]);
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add NatGov',
            'action' => route('natgov.store'),
            'method' => 'POST',
            'cancelRoute' => route('natgov'),
            'fields' => $this->fields(),
            'item' => new NatGov(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        [$data['document_path'], $data['draft_documents']] = $this->appendUploadedFiles(
            $request,
            'document_path',
            'document_paths',
            [],
            null,
            'uploads/natgov/drafts'
        );
        if (Schema::hasColumn('nat_govs', 'approved_document_path')) {
            [$data['approved_document_path'], $data['approved_documents']] = $this->appendUploadedFiles(
                $request,
                'approved_document_path',
                'approved_document_paths',
                [],
                null,
                'uploads/natgov/approved'
            );
        }

        $natgov = NatGov::create($this->filterPersistableData($data));
        $this->syncDeadlineTownHallMemo(
            $natgov,
            $natgov->deadline_date?->toDateString(),
            'NatGov',
            trim(($natgov->agency ?: 'NatGov filing') . ' - ' . ($natgov->client ?: $natgov->registration_no ?: 'Untitled Record'), ' -'),
            'natgov.preview'
        );

        return redirect()->route('natgov')->with('success', 'NatGov entry created.');
    }

    public function show(NatGov $natgov)
    {
        $generatedDraftPath = null;
        if (!$natgov->document_path) {
            $generatedDraftPath = $this->generatePdfPreview(
                'corporate.natgov.pdf',
                ['natgov' => $natgov],
                'generated-previews/natgov/' . ($natgov->registration_no ?: $natgov->id) . '-draft.pdf'
            );
        }

        $uploadUrl = function (?string $path): ?string {
            if (!$path || !Storage::disk('public')->exists($path)) {
                return null;
            }

            $segments = array_map('rawurlencode', array_values(array_filter(explode('/', trim($path, '/')), fn ($segment) => $segment !== '')));

            return url('/uploads/' . implode('/', $segments));
        };

        $draftDocuments = collect($natgov->draft_documents ?? [])->filter(fn ($entry) => !empty($entry['path']) && Storage::disk('public')->exists($entry['path']))->values();
        $approvedDocuments = collect($natgov->approved_documents ?? [])->filter(fn ($entry) => !empty($entry['path']) && Storage::disk('public')->exists($entry['path']))->values();
        $draftOptions = $draftDocuments->map(function ($entry, $index) use ($uploadUrl) {
            return [
                'url' => $uploadUrl($entry['path']),
                'label' => $entry['name'] ?? ('Draft Revision ' . ($index + 1)),
                'uploaded_at' => $entry['uploaded_at'] ?? null,
            ];
        })->values()->all();

        $draftUrl = $uploadUrl($natgov->document_path) ?: ($generatedDraftPath && Storage::disk('public')->exists($generatedDraftPath) ? route('uploads.show', ['path' => $generatedDraftPath]) : null);
        $approvedUrl = $uploadUrl($natgov->approved_document_path);

        return view('corporate.natgov.preview', [
            'natgov' => $natgov,
            'generatedDraftUrl' => $generatedDraftPath && Storage::disk('public')->exists($generatedDraftPath) ? route('uploads.show', ['path' => $generatedDraftPath]) : null,
            'draftUrl' => $draftUrl,
            'approvedUrl' => $approvedUrl,
            'draftDocuments' => $draftDocuments,
            'approvedDocuments' => $approvedDocuments,
            'draftOptions' => $draftOptions,
            'selectedDraftUrl' => !empty($draftOptions) ? $draftOptions[array_key_last($draftOptions)]['url'] : $draftUrl,
            'latestDraft' => $draftDocuments->last(),
            'latestApproved' => $approvedDocuments->last(),
            'visibleAuthorityNotes' => $this->visibleAuthorityNotes($natgov),
            'backRoute' => route('natgov'),
            'editRoute' => route('natgov.edit', $natgov),
            'deleteRoute' => route('natgov.destroy', $natgov),
            'updateRoute' => route('natgov.update', $natgov),
        ]);
    }

    public function edit(NatGov $natgov)
    {
        return view('corporate.common.form', [
            'title' => 'Edit NatGov',
            'action' => route('natgov.update', $natgov),
            'method' => 'PUT',
            'cancelRoute' => route('natgov'),
            'fields' => $this->fields(),
            'item' => $natgov,
        ]);
    }

    public function update(Request $request, NatGov $natgov)
    {
        $data = $this->validateData($request);
        [$data['document_path'], $data['draft_documents']] = $this->appendUploadedFiles(
            $request,
            'document_path',
            'document_paths',
            $natgov->draft_documents ?? [],
            $natgov->document_path,
            'uploads/natgov/drafts'
        );
        if (Schema::hasColumn('nat_govs', 'approved_document_path')) {
            [$data['approved_document_path'], $data['approved_documents']] = $this->appendUploadedFiles(
                $request,
                'approved_document_path',
                'approved_document_paths',
                $natgov->approved_documents ?? [],
                $natgov->approved_document_path,
                'uploads/natgov/approved'
            );
        }

        $natgov->update($this->filterPersistableData($data));
        $natgov->refresh();
        $this->syncDeadlineTownHallMemo(
            $natgov,
            $natgov->deadline_date?->toDateString(),
            'NatGov',
            trim(($natgov->agency ?: 'NatGov filing') . ' - ' . ($natgov->client ?: $natgov->registration_no ?: 'Untitled Record'), ' -'),
            'natgov.preview'
        );

        return redirect()->route('natgov')->with('success', 'NatGov entry updated.');
    }

    public function destroy(NatGov $natgov)
    {
        $this->deleteDeadlineTownHallMemo($natgov);
        $natgov->delete();

        return redirect()->route('natgov')->with('success', 'NatGov entry deleted.');
    }

    public function storeAuthorityNote(Request $request, NatGov $natgov)
    {
        $data = $request->validate([
            'visible_to_role' => ['required', 'string', 'in:Admin,Employee'],
            'body' => ['required', 'string'],
        ]);

        $natgov->authorityNotes()->create([
            'user_id' => auth()->id(),
            'visible_to_role' => $data['visible_to_role'],
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('natgov.preview', $natgov)
            ->with('success', 'Authority note added.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'client', 'label' => 'Company', 'type' => 'text'],
            ['name' => 'tin', 'label' => 'TIN', 'type' => 'text'],
            ['name' => 'agency', 'label' => 'Government Body/Agency', 'type' => 'text'],
            ['name' => 'registration_status', 'label' => 'Registration Status', 'type' => 'text'],
            ['name' => 'registration_date', 'label' => 'Registration Date', 'type' => 'date'],
            ['name' => 'deadline_date', 'label' => 'Deadline Date', 'type' => 'date'],
            ['name' => 'registration_no', 'label' => 'Registration No.', 'type' => 'text'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'text'],
            ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
            ['name' => 'date_uploaded', 'label' => 'Date Uploaded', 'type' => 'date'],
            ['name' => 'document_path', 'label' => 'Upload Draft NatGov Document (PDF)', 'type' => 'file'],
            ['name' => 'approved_document_path', 'label' => 'Upload Approved NatGov Document (PDF)', 'type' => 'file'],
            ['name' => 'notes_visible_to', 'label' => 'Notes Visible To Authority', 'type' => 'text'],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'client' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'agency' => ['nullable', 'string', 'max:255'],
            'registration_status' => ['nullable', 'string', 'max:255'],
            'registration_date' => ['nullable', 'date'],
            'deadline_date' => ['nullable', 'date'],
            'registration_no' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
            'date_uploaded' => ['nullable', 'date'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'document_paths' => ['nullable', 'array'],
            'document_paths.*' => ['file', 'mimes:pdf', 'max:5120'],
            'approved_document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'approved_document_paths' => ['nullable', 'array'],
            'approved_document_paths.*' => ['file', 'mimes:pdf', 'max:5120'],
            'notes_visible_to' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function companyDefaults(): array
    {
        if (Schema::hasTable('gis_records')) {
            $gis = GisRecord::where('approval_status', 'Approved')->latest()->first();

            if ($gis) {
                return [
                    'client' => $gis->corporation_name ?: 'JK&C Group of Companies',
                    'tin' => $gis->tin ?: '000-000-000-000',
                ];
            }
        }

        return [
            'client' => 'JK&C Group of Companies',
            'tin' => '000-000-000-000',
        ];
    }

    private function filterPersistableData(array $data): array
    {
        return collect($data)
            ->filter(fn ($value, $key) => Schema::hasColumn('nat_govs', $key))
            ->all();
    }

    private function visibleAuthorityNotes(NatGov $natgov)
    {
        $role = auth()->user()?->role;

        return $natgov->authorityNotes()
            ->with('user:id,name,role')
            ->when($role && $role !== 'SuperAdmin', function ($query) use ($role) {
                $query->where('visible_to_role', $role);
            })
            ->get();
    }
}
