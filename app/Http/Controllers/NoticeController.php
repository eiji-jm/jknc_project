<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        $notices = Notice::with(['minutes', 'resolutions', 'secretaryCertificates'])->latest()->get();

        return view('corporate.notices.index', compact('notices'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Notice of Meeting',
            'action' => route('notices.store'),
            'method' => 'POST',
            'cancelRoute' => route('notices'),
            'fields' => $this->fields(),
            'item' => new Notice(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path');

        Notice::create($data);

        return redirect()->route('notices')->with('success', 'Notice created.');
    }

    public function show(Notice $notice)
    {
        $notice->load(['minutes', 'resolutions', 'secretaryCertificates']);

        return view('corporate.notices.preview', [
            'notice' => $notice,
        ]);
    }

    public function edit(Notice $notice)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Notice of Meeting',
            'action' => route('notices.update', $notice),
            'method' => 'PUT',
            'cancelRoute' => route('notices'),
            'fields' => $this->fields(),
            'item' => $notice,
        ]);
    }

    public function update(Request $request, Notice $notice)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $notice->document_path);

        $notice->update($data);

        return redirect()->route('notices')->with('success', 'Notice updated.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();

        return redirect()->route('notices')->with('success', 'Notice deleted.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'notice_number', 'label' => 'Notice Number', 'type' => 'text'],
            ['name' => 'date_of_notice', 'label' => 'Date of Notice', 'type' => 'date'],
            ['name' => 'governing_body', 'label' => 'Governing Body', 'type' => 'select', 'options' => $this->governingBodyOptions()],
            ['name' => 'type_of_meeting', 'label' => 'Type of Meeting', 'type' => 'select', 'options' => $this->meetingTypeOptions()],
            ['name' => 'date_of_meeting', 'label' => 'Date of Meeting', 'type' => 'date'],
            ['name' => 'time_started', 'label' => 'Time Started', 'type' => 'time'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'meeting_no', 'label' => 'Meeting Number', 'type' => 'text'],
            ['name' => 'chairman', 'label' => 'Chairman', 'type' => 'text'],
            ['name' => 'secretary', 'label' => 'Secretary', 'type' => 'text'],
            ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
            ['name' => 'date_updated', 'label' => 'Date Updated', 'type' => 'date'],
            ['name' => 'body_html', 'label' => 'Notice Body', 'type' => 'textarea'],
            ['name' => 'body_mode', 'label' => 'Body Mode', 'type' => 'select', 'options' => ['builder', 'upload']],
            ['name' => 'document_path', 'label' => 'Upload Notice (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'notice_number' => ['nullable', 'string', 'max:255'],
            'date_of_notice' => ['nullable', 'date'],
            'governing_body' => ['nullable', 'string', 'max:255'],
            'type_of_meeting' => ['nullable', 'string', 'max:255'],
            'date_of_meeting' => ['nullable', 'date'],
            'time_started' => ['nullable'],
            'location' => ['nullable', 'string', 'max:255'],
            'meeting_no' => ['nullable', 'string', 'max:255'],
            'chairman' => ['nullable', 'string', 'max:255'],
            'secretary' => ['nullable', 'string', 'max:255'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
            'date_updated' => ['nullable', 'date'],
            'body_html' => ['nullable', 'string'],
            'body_mode' => ['nullable', 'string', 'max:50'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
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
