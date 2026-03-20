<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\Resolution;
use Illuminate\Http\Request;

class ResolutionController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        $resolutions = Resolution::latest()->get();

        return view('corporate.resolutions.index', compact('resolutions'));
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
        $data['draft_file_path'] = $this->handleUpload($request, 'draft_file_path');
        $data['notarized_file_path'] = $this->handleUpload($request, 'notarized_file_path');

        Resolution::create($data);

        return redirect()->route('resolutions')->with('success', 'Resolution created.');
    }

    public function show(Resolution $resolution)
    {
        return view('corporate.resolutions.preview', [
            'resolution' => $resolution,
            'backRoute' => route('resolutions'),
            'editRoute' => route('resolutions.edit', $resolution),
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
        $data['draft_file_path'] = $this->handleUpload($request, 'draft_file_path', $resolution->draft_file_path);
        $data['notarized_file_path'] = $this->handleUpload($request, 'notarized_file_path', $resolution->notarized_file_path);

        $resolution->update($data);

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
            ['name' => 'governing_body', 'label' => 'Governing Body', 'type' => 'text'],
            ['name' => 'type_of_meeting', 'label' => 'Type of Meeting', 'type' => 'text'],
            ['name' => 'notice_ref', 'label' => 'Notice Reference #', 'type' => 'text'],
            ['name' => 'meeting_no', 'label' => 'Meeting No.', 'type' => 'text'],
            ['name' => 'date_of_meeting', 'label' => 'Date of Meeting', 'type' => 'date'],
            ['name' => 'location', 'label' => 'Location of Meeting', 'type' => 'text'],
            ['name' => 'board_resolution', 'label' => 'Board Resolution', 'type' => 'text'],
            ['name' => 'directors', 'label' => 'Directors', 'type' => 'text'],
            ['name' => 'chairman', 'label' => 'Chairman', 'type' => 'text'],
            ['name' => 'secretary', 'label' => 'Secretary', 'type' => 'text'],
            ['name' => 'notary_doc_no', 'label' => 'Notary Doc No.', 'type' => 'text'],
            ['name' => 'notary_page_no', 'label' => 'Notary Page No.', 'type' => 'text'],
            ['name' => 'notary_book_no', 'label' => 'Notary Book No.', 'type' => 'text'],
            ['name' => 'notary_series_no', 'label' => 'Notary Series No.', 'type' => 'text'],
            ['name' => 'notary_public', 'label' => 'Notary Public', 'type' => 'text'],
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
            'governing_body' => ['nullable', 'string', 'max:255'],
            'type_of_meeting' => ['nullable', 'string', 'max:255'],
            'notice_ref' => ['nullable', 'string', 'max:255'],
            'meeting_no' => ['nullable', 'string', 'max:255'],
            'date_of_meeting' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'board_resolution' => ['nullable', 'string', 'max:255'],
            'directors' => ['nullable', 'string', 'max:255'],
            'chairman' => ['nullable', 'string', 'max:255'],
            'secretary' => ['nullable', 'string', 'max:255'],
            'notary_doc_no' => ['nullable', 'string', 'max:255'],
            'notary_page_no' => ['nullable', 'string', 'max:255'],
            'notary_book_no' => ['nullable', 'string', 'max:255'],
            'notary_series_no' => ['nullable', 'string', 'max:255'],
            'notary_public' => ['nullable', 'string', 'max:255'],
            'draft_file_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'notarized_file_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }
}
