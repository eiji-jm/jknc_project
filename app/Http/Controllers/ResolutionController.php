<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\Minute;
use App\Models\Resolution;
use Illuminate\Http\Request;

class ResolutionController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        $resolutions = Resolution::with(['minute', 'notice', 'secretaryCertificates'])->latest()->get();
        $minutes = Minute::with('notice')->orderBy('date_of_meeting')->get();

        return view('corporate.resolutions.index', compact('resolutions', 'minutes'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Resolution',
            'action' => route('resolutions.store'),
            'method' => 'POST',
            'cancelRoute' => route('resolutions'),
            'fields' => $this->fields(),
            'item' => new Resolution(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data = $this->mergeMinuteData($data);
        $data['draft_file_path'] = $this->handleUpload($request, 'draft_file_path');
        $data['notarized_file_path'] = $this->handleUpload($request, 'notarized_file_path');

        $resolution = Resolution::create($data);
        $this->syncSecretaryCertificates($resolution);

        return redirect()->route('resolutions')->with('success', 'Resolution created.');
    }

    public function show(Resolution $resolution)
    {
        $resolution->load(['notice', 'secretaryCertificates']);

        return view('corporate.resolutions.preview', [
            'resolution' => $resolution,
            'backRoute' => route('resolutions'),
            'editRoute' => route('resolutions.edit', $resolution),
            'updateRoute' => route('resolutions.update', $resolution),
            'deleteRoute' => route('resolutions.destroy', $resolution),
        ]);
    }

    public function edit(Resolution $resolution)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Resolution',
            'action' => route('resolutions.update', $resolution),
            'method' => 'PUT',
            'cancelRoute' => route('resolutions'),
            'fields' => $this->fields(),
            'item' => $resolution,
        ]);
    }

    public function update(Request $request, Resolution $resolution)
    {
        $data = $this->validateData($request);
        $data = $this->mergeMinuteData($data);
        $data['draft_file_path'] = $this->handleUpload($request, 'draft_file_path', $resolution->draft_file_path);
        $data['notarized_file_path'] = $this->handleUpload($request, 'notarized_file_path', $resolution->notarized_file_path);

        $resolution->update($data);
        $this->syncSecretaryCertificates($resolution->fresh());

        return redirect()->route('resolutions')->with('success', 'Resolution updated.');
    }

    public function destroy(Resolution $resolution)
    {
        $resolution->delete();

        return redirect()->route('resolutions')->with('success', 'Resolution deleted.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'resolution_no', 'label' => 'Resolution No.', 'type' => 'text'],
            ['name' => 'date_uploaded', 'label' => 'Date Uploaded', 'type' => 'date'],
            ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
            ['name' => 'minute_id', 'label' => 'Linked Minutes', 'type' => 'text'],
            ['name' => 'governing_body', 'label' => 'Governing Body', 'type' => 'select', 'options' => $this->governingBodyOptions()],
            ['name' => 'type_of_meeting', 'label' => 'Type of Meeting', 'type' => 'select', 'options' => $this->meetingTypeOptions()],
            ['name' => 'notice_id', 'label' => 'Linked Notice', 'type' => 'text'],
            ['name' => 'notice_ref', 'label' => 'Notice Reference #', 'type' => 'text'],
            ['name' => 'meeting_no', 'label' => 'Meeting No.', 'type' => 'text'],
            ['name' => 'date_of_meeting', 'label' => 'Date of Meeting', 'type' => 'date'],
            ['name' => 'location', 'label' => 'Location of Meeting', 'type' => 'text'],
            ['name' => 'board_resolution', 'label' => 'Board Resolution', 'type' => 'text'],
            ['name' => 'resolution_body', 'label' => 'Resolution Body', 'type' => 'textarea'],
            ['name' => 'directors', 'label' => 'Directors', 'type' => 'text'],
            ['name' => 'chairman', 'label' => 'Chairman', 'type' => 'text'],
            ['name' => 'secretary', 'label' => 'Secretary', 'type' => 'text'],
            ['name' => 'notary_doc_no', 'label' => 'Notary Doc No.', 'type' => 'text'],
            ['name' => 'notary_page_no', 'label' => 'Notary Page No.', 'type' => 'text'],
            ['name' => 'notary_book_no', 'label' => 'Notary Book No.', 'type' => 'text'],
            ['name' => 'notary_series_no', 'label' => 'Notary Series No.', 'type' => 'text'],
            ['name' => 'notary_public', 'label' => 'Notary Public', 'type' => 'text'],
            ['name' => 'notarized_on', 'label' => 'Notarized On', 'type' => 'date'],
            ['name' => 'notarized_at', 'label' => 'Notarized At', 'type' => 'text'],
            ['name' => 'draft_file_path', 'label' => 'Upload Draft (PDF)', 'type' => 'file'],
            ['name' => 'notarized_file_path', 'label' => 'Upload Notarized (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'resolution_no' => ['nullable', 'string', 'max:255'],
            'date_uploaded' => ['nullable', 'date'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
            'minute_id' => ['nullable', 'integer', 'exists:minutes,id', 'required_without:notice_id'],
            'governing_body' => ['nullable', 'string', 'max:255'],
            'type_of_meeting' => ['nullable', 'string', 'max:255'],
            'notice_id' => ['nullable', 'integer', 'exists:notices,id'],
            'notice_ref' => ['nullable', 'string', 'max:255'],
            'meeting_no' => ['nullable', 'string', 'max:255'],
            'date_of_meeting' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'board_resolution' => ['nullable', 'string', 'max:255'],
            'resolution_body' => ['nullable', 'string'],
            'directors' => ['nullable', 'string', 'max:255'],
            'chairman' => ['nullable', 'string', 'max:255'],
            'secretary' => ['nullable', 'string', 'max:255'],
            'notary_doc_no' => ['nullable', 'string', 'max:255'],
            'notary_page_no' => ['nullable', 'string', 'max:255'],
            'notary_book_no' => ['nullable', 'string', 'max:255'],
            'notary_series_no' => ['nullable', 'string', 'max:255'],
            'notary_public' => ['nullable', 'string', 'max:255'],
            'notarized_on' => ['nullable', 'date'],
            'notarized_at' => ['nullable', 'string', 'max:255'],
            'draft_file_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'notarized_file_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function mergeMinuteData(array $data): array
    {
        if (empty($data['minute_id'])) {
            return $data;
        }

        $minute = Minute::with('notice')->find($data['minute_id']);
        if (!$minute) {
            return $data;
        }

        $notice = $minute->notice;

        $data['notice_id'] = $minute->notice_id;
        $data['notice_ref'] = $minute->notice_ref ?: $notice?->notice_number;
        $data['governing_body'] = $minute->governing_body ?: $notice?->governing_body;
        $data['type_of_meeting'] = $minute->type_of_meeting ?: $notice?->type_of_meeting;
        $data['meeting_no'] = $minute->meeting_no ?: $notice?->meeting_no;
        $data['date_of_meeting'] = optional($minute->date_of_meeting)?->toDateString()
            ?: optional($notice?->date_of_meeting)?->toDateString();
        $data['location'] = $minute->location ?: $notice?->location;
        $data['chairman'] = $minute->chairman ?: $notice?->chairman;
        $data['secretary'] = $minute->secretary ?: $notice?->secretary;

        return $data;
    }

    private function syncSecretaryCertificates(Resolution $resolution): void
    {
        $shared = [
            'notice_id' => $resolution->notice_id,
            'notice_ref' => $resolution->notice_ref,
            'resolution_no' => $resolution->resolution_no,
            'governing_body' => $resolution->governing_body,
            'type_of_meeting' => $resolution->type_of_meeting,
            'meeting_no' => $resolution->meeting_no,
            'date_of_meeting' => $resolution->date_of_meeting,
            'location' => $resolution->location,
            'secretary' => $resolution->secretary,
            'purpose' => $resolution->board_resolution,
            'notary_doc_no' => $resolution->notary_doc_no,
            'notary_page_no' => $resolution->notary_page_no,
            'notary_book_no' => $resolution->notary_book_no,
            'notary_series_no' => $resolution->notary_series_no,
            'notary_public' => $resolution->notary_public,
        ];

        $resolution->secretaryCertificates()->get()->each(function ($certificate) use ($shared) {
            $certificate->update($shared);
        });
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
