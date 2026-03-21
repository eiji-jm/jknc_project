<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\Notice;
use App\Models\Resolution;
use App\Models\SecretaryCertificate;
use Illuminate\Http\Request;

class SecretaryCertificateController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        $certificates = SecretaryCertificate::with(['notice', 'resolution'])->latest()->get();
        $resolutions = Resolution::with('notice')->orderBy('date_of_meeting')->get();

        return view('corporate.secretary-certificates.index', compact('certificates', 'resolutions'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Secretary Certificate',
            'action' => route('secretary-certificates.store'),
            'method' => 'POST',
            'cancelRoute' => route('secretary-certificates'),
            'fields' => $this->fields(),
            'item' => new SecretaryCertificate(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data = $this->mergeResolutionData($data);
        $data['document_path'] = $this->handleUpload($request, 'document_path');

        SecretaryCertificate::create($data);

        return redirect()->route('secretary-certificates')->with('success', 'Secretary certificate created.');
    }

    public function show(SecretaryCertificate $secretaryCertificate)
    {
        $secretaryCertificate->load(['notice', 'resolution.notice']);

        return view('corporate.secretary-certificates.preview', [
            'certificate' => $secretaryCertificate,
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
        $data = $this->mergeResolutionData($data);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $secretaryCertificate->document_path);

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
            ['name' => 'notice_id', 'label' => 'Linked Notice', 'type' => 'text'],
            ['name' => 'notice_ref', 'label' => 'Notice Reference #', 'type' => 'text'],
            ['name' => 'meeting_no', 'label' => 'Meeting No.', 'type' => 'text'],
            ['name' => 'resolution_id', 'label' => 'Linked Resolution', 'type' => 'text'],
            ['name' => 'resolution_no', 'label' => 'Resolution No.', 'type' => 'text'],
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
            'notice_id' => ['nullable', 'integer', 'exists:notices,id'],
            'notice_ref' => ['nullable', 'string', 'max:255'],
            'meeting_no' => ['nullable', 'string', 'max:255'],
            'resolution_id' => ['nullable', 'integer', 'exists:resolutions,id'],
            'resolution_no' => ['nullable', 'string', 'max:255'],
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

    private function mergeResolutionData(array $data): array
    {
        if (empty($data['resolution_id'])) {
            return $data;
        }

        $resolution = Resolution::with('notice')->find($data['resolution_id']);
        if (!$resolution) {
            return $data;
        }

        $data['notice_id'] = $resolution->notice_id;
        $data['notice_ref'] = $resolution->notice_ref;
        $data['resolution_no'] = $resolution->resolution_no;
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

    private function governingBodyOptions(): array
    {
        return ['Stockholders', 'Board of Directors', 'Joint Stockholders and Board of Directors'];
    }

    private function meetingTypeOptions(): array
    {
        return ['Regular', 'Special'];
    }
}
