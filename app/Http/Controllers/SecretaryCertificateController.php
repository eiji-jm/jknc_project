<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesCorporateDocumentNumbers;
use App\Http\Controllers\Concerns\GeneratesPdfPreview;
use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\Minute;
use App\Models\Notice;
use App\Models\Resolution;
use App\Models\SecretaryCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SecretaryCertificateController extends Controller
{
    use GeneratesCorporateDocumentNumbers;
    use GeneratesPdfPreview;
    use HandlesUploads;

    public function index()
    {
        $certificates = SecretaryCertificate::with(['notice', 'resolution', 'minute'])->latest()->get();
        $resolutions = Resolution::with(['notice', 'minute'])->orderBy('date_of_meeting')->get();
        $minutes = Minute::with('notice')->orderBy('date_of_meeting')->get();

        return view('corporate.secretary-certificates.index', [
            'certificates' => $certificates,
            'resolutions' => $resolutions,
            'minutes' => $minutes,
            'nextCertificateNumber' => $this->nextSecretaryCertificateNumber(),
        ]);
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Secretary Certificate',
            'action' => route('secretary-certificates.store'),
            'method' => 'POST',
            'cancelRoute' => route('secretary-certificates'),
            'fields' => $this->fields(),
            'item' => new SecretaryCertificate([
                'certificate_no' => $this->nextSecretaryCertificateNumber(),
                'date_uploaded' => now()->toDateString(),
                'uploaded_by' => auth()->user()?->name ?? '',
                'date_issued' => now()->toDateString(),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data = $this->mergeMeetingSourceData($data);
        $data['document_path'] = $this->handleUpload($request, 'document_path');
        $data['certificate_no'] = $data['certificate_no'] ?: $this->nextSecretaryCertificateNumber();
        $data = $this->filterPersistableData($data);

        SecretaryCertificate::create($data);

        return redirect()->route('secretary-certificates')->with('success', 'Secretary certificate created.');
    }

    public function show(SecretaryCertificate $secretaryCertificate)
    {
        $secretaryCertificate->load(['notice', 'resolution.notice', 'minute.notice']);
        $generatedDraftPath = $this->generatePdfPreview(
            'corporate.secretary-certificates.pdf',
            ['certificate' => $secretaryCertificate],
            'generated-previews/secretary-certificates/' . ($secretaryCertificate->certificate_no ?: $secretaryCertificate->id) . '-draft.pdf'
        );

        return view('corporate.secretary-certificates.preview', [
            'certificate' => $secretaryCertificate,
            'generatedDraftUrl' => $generatedDraftPath ? route('uploads.show', ['path' => $generatedDraftPath]) : null,
            'backRoute' => route('secretary-certificates'),
            'editRoute' => route('secretary-certificates.edit', $secretaryCertificate),
            'updateRoute' => route('secretary-certificates.update', $secretaryCertificate),
            'deleteRoute' => route('secretary-certificates.destroy', $secretaryCertificate),
        ]);
    }

    public function edit(SecretaryCertificate $secretaryCertificate)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Secretary Certificate',
            'action' => route('secretary-certificates.update', $secretaryCertificate),
            'method' => 'PUT',
            'cancelRoute' => route('secretary-certificates'),
            'fields' => $this->fields(),
            'item' => $secretaryCertificate,
        ]);
    }

    public function update(Request $request, SecretaryCertificate $secretaryCertificate)
    {
        $data = $this->validateData($request);
        $data = $this->mergeMeetingSourceData($data);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $secretaryCertificate->document_path);
        $data = $this->filterPersistableData($data);

        $secretaryCertificate->update($data);

        return redirect()->route('secretary-certificates')->with('success', 'Secretary certificate updated.');
    }

    public function destroy(SecretaryCertificate $secretaryCertificate)
    {
        $secretaryCertificate->delete();

        return redirect()->route('secretary-certificates')->with('success', 'Secretary certificate deleted.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'certificate_no', 'label' => 'Certificate No.', 'type' => 'text'],
            ['name' => 'date_uploaded', 'label' => 'Date Uploaded', 'type' => 'date'],
            ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
            ['name' => 'governing_body', 'label' => 'Governing Body', 'type' => 'select', 'options' => $this->governingBodyOptions()],
            ['name' => 'type_of_meeting', 'label' => 'Type of Meeting', 'type' => 'select', 'options' => $this->meetingTypeOptions()],
            ['name' => 'minute_id', 'label' => 'Linked Minutes', 'type' => 'text'],
            ['name' => 'notice_id', 'label' => 'Linked Notice', 'type' => 'text'],
            ['name' => 'notice_ref', 'label' => 'Notice Reference #', 'type' => 'text'],
            ['name' => 'meeting_no', 'label' => 'Meeting No.', 'type' => 'text'],
            ['name' => 'minutes_ref', 'label' => 'Minutes Ref.', 'type' => 'text'],
            ['name' => 'resolution_id', 'label' => 'Linked Resolution', 'type' => 'text'],
            ['name' => 'resolution_no', 'label' => 'Resolution No.', 'type' => 'text'],
            ['name' => 'resolution_body', 'label' => 'Resolution Body', 'type' => 'textarea'],
            ['name' => 'date_issued', 'label' => 'Date Issued', 'type' => 'date'],
            ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'text'],
            ['name' => 'date_of_meeting', 'label' => 'Date of Meeting', 'type' => 'date'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'secretary', 'label' => 'Secretary', 'type' => 'text'],
            ['name' => 'notary_doc_no', 'label' => 'Notary Doc No.', 'type' => 'text'],
            ['name' => 'notary_page_no', 'label' => 'Notary Page No.', 'type' => 'text'],
            ['name' => 'notary_book_no', 'label' => 'Notary Book No.', 'type' => 'text'],
            ['name' => 'notary_series_no', 'label' => 'Notary Series No.', 'type' => 'text'],
            ['name' => 'notary_public', 'label' => 'Notary Public', 'type' => 'text'],
            ['name' => 'document_path', 'label' => 'Upload Original Certificate (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'certificate_no' => ['nullable', 'string', 'max:255'],
            'date_uploaded' => ['nullable', 'date'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
            'governing_body' => ['nullable', 'string', 'max:255'],
            'type_of_meeting' => ['nullable', 'string', 'max:255'],
            'minute_id' => ['nullable', 'integer', 'exists:minutes,id', 'required_without:resolution_id'],
            'notice_id' => ['nullable', 'integer', 'exists:notices,id'],
            'notice_ref' => ['nullable', 'string', 'max:255'],
            'meeting_no' => ['nullable', 'string', 'max:255'],
            'minutes_ref' => ['nullable', 'string', 'max:255'],
            'resolution_id' => ['nullable', 'integer', 'exists:resolutions,id', 'required_without:minute_id'],
            'resolution_no' => ['nullable', 'string', 'max:255'],
            'resolution_body' => ['nullable', 'string'],
            'date_issued' => ['nullable', 'date'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'date_of_meeting' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'secretary' => ['nullable', 'string', 'max:255'],
            'notary_doc_no' => ['nullable', 'string', 'max:255'],
            'notary_page_no' => ['nullable', 'string', 'max:255'],
            'notary_book_no' => ['nullable', 'string', 'max:255'],
            'notary_series_no' => ['nullable', 'string', 'max:255'],
            'notary_public' => ['nullable', 'string', 'max:255'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function mergeMeetingSourceData(array $data): array
    {
        if (!empty($data['resolution_id'])) {
            $resolution = Resolution::with(['notice', 'minute'])->find($data['resolution_id']);
            if (!$resolution) {
                return $data;
            }

            $data['minute_id'] = $resolution->minute_id;
            $data['minutes_ref'] = $resolution->minute?->minutes_ref;
            $data['notice_id'] = $resolution->notice_id;
            $data['notice_ref'] = $resolution->notice_ref;
            $data['resolution_no'] = $resolution->resolution_no;
            $data['resolution_body'] = $data['resolution_body'] ?: $resolution->resolution_body;
            $data['governing_body'] = $resolution->governing_body;
            $data['type_of_meeting'] = $resolution->type_of_meeting;
            $data['meeting_no'] = $resolution->meeting_no;
            $data['purpose'] = $data['purpose'] ?: $resolution->board_resolution;
            $data['date_of_meeting'] = $resolution->date_of_meeting;
            $data['location'] = $resolution->location;
            $data['secretary'] = $resolution->secretary;
            $data['notary_doc_no'] = $resolution->notary_doc_no;
            $data['notary_page_no'] = $resolution->notary_page_no;
            $data['notary_book_no'] = $resolution->notary_book_no;
            $data['notary_series_no'] = $resolution->notary_series_no;
            $data['notary_public'] = $resolution->notary_public;

            return $data;
        }

        if (empty($data['minute_id'])) {
            return $data;
        }

        $minute = Minute::with('notice')->find($data['minute_id']);
        if (!$minute) {
            return $data;
        }

        $notice = $minute->notice;
        $data['minutes_ref'] = $minute->minutes_ref;
        $data['notice_id'] = $minute->notice_id;
        $data['notice_ref'] = $minute->notice_ref ?: $notice?->notice_number;
        $data['governing_body'] = $minute->governing_body ?: $notice?->governing_body;
        $data['type_of_meeting'] = $minute->type_of_meeting ?: $notice?->type_of_meeting;
        $data['meeting_no'] = $minute->meeting_no ?: $notice?->meeting_no;
        $data['date_of_meeting'] = $minute->date_of_meeting ?: $notice?->date_of_meeting;
        $data['location'] = $minute->location ?: $notice?->location;
        $data['secretary'] = $minute->secretary ?: $notice?->secretary;
        $data['resolution_id'] = null;
        $data['resolution_no'] = $data['resolution_no'] ?: null;
        $data['resolution_body'] = $data['resolution_body'] ?: ('Certified from Minutes Ref. ' . ($minute->minutes_ref ?: '') . '.');
        $data['purpose'] = $data['purpose'] ?: ('Certified extract from Minutes Ref. ' . ($minute->minutes_ref ?: ''));

        return $data;
    }

    private function governingBodyOptions(): array
    {
        return ['Stockholders', 'Board of Directors', 'Joint Stockholders and Board of Directors'];
    }

    private function meetingTypeOptions(): array
    {
        return ['Regular', 'Special'];
    }

    private function filterPersistableData(array $data): array
    {
        return collect($data)
            ->filter(fn ($value, $key) => Schema::hasColumn('secretary_certificates', $key))
            ->all();
    }
}
