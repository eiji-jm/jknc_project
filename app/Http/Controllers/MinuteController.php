<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\Minute;
use Illuminate\Http\Request;

class MinuteController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        $minutes = Minute::latest()->get();

        return view('corporate.minutes.index', compact('minutes'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Minutes of Meeting',
            'action' => route('minutes.store'),
            'method' => 'POST',
            'cancelRoute' => route('minutes'),
            'fields' => $this->fields(),
            'item' => new Minute(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path');

        Minute::create($data);

        return redirect()->route('minutes')->with('success', 'Minutes created.');
    }

    public function show(Minute $minute)
    {
        return view('corporate.minutes.preview', [
            'minute' => $minute,
            'backRoute' => route('minutes'),
            'editRoute' => route('minutes.edit', $minute),
            'deleteRoute' => route('minutes.destroy', $minute),
        ]);
    }

    public function edit(Minute $minute)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Minutes of Meeting',
            'action' => route('minutes.update', $minute),
            'method' => 'PUT',
            'cancelRoute' => route('minutes'),
            'fields' => $this->fields(),
            'item' => $minute,
        ]);
    }

    public function update(Request $request, Minute $minute)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $minute->document_path);

        $minute->update($data);

        return redirect()->route('minutes')->with('success', 'Minutes updated.');
    }

    public function destroy(Minute $minute)
    {
        $minute->delete();

        return redirect()->route('minutes')->with('success', 'Minutes deleted.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'minutes_ref', 'label' => 'Minutes Reference', 'type' => 'text'],
            ['name' => 'date_uploaded', 'label' => 'Date Uploaded', 'type' => 'date'],
            ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
            ['name' => 'governing_body', 'label' => 'Governing Body', 'type' => 'text'],
            ['name' => 'type_of_meeting', 'label' => 'Type of Meeting', 'type' => 'text'],
            ['name' => 'meeting_mode', 'label' => 'Meeting Mode', 'type' => 'text'],
            ['name' => 'notice_ref', 'label' => 'Notice Reference #', 'type' => 'text'],
            ['name' => 'date_of_meeting', 'label' => 'Date of Meeting', 'type' => 'date'],
            ['name' => 'time_started', 'label' => 'Time Started', 'type' => 'time'],
            ['name' => 'time_ended', 'label' => 'Time Ended', 'type' => 'time'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'call_link', 'label' => 'Call Link', 'type' => 'text'],
            ['name' => 'recording_notes', 'label' => 'Recording Notes', 'type' => 'text'],
            ['name' => 'meeting_no', 'label' => 'Meeting Number', 'type' => 'text'],
            ['name' => 'chairman', 'label' => 'Chairman', 'type' => 'text'],
            ['name' => 'secretary', 'label' => 'Secretary', 'type' => 'text'],
            ['name' => 'document_path', 'label' => 'Upload Minutes (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'minutes_ref' => ['nullable', 'string', 'max:255'],
            'date_uploaded' => ['nullable', 'date'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
            'governing_body' => ['nullable', 'string', 'max:255'],
            'type_of_meeting' => ['nullable', 'string', 'max:255'],
            'meeting_mode' => ['nullable', 'string', 'max:255'],
            'notice_ref' => ['nullable', 'string', 'max:255'],
            'date_of_meeting' => ['nullable', 'date'],
            'time_started' => ['nullable'],
            'time_ended' => ['nullable'],
            'location' => ['nullable', 'string', 'max:255'],
            'call_link' => ['nullable', 'string', 'max:255'],
            'recording_notes' => ['nullable', 'string', 'max:255'],
            'meeting_no' => ['nullable', 'string', 'max:255'],
            'chairman' => ['nullable', 'string', 'max:255'],
            'secretary' => ['nullable', 'string', 'max:255'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }
}
