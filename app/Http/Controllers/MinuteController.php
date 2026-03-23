<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesCorporateDocumentNumbers;
use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\Minute;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MinuteController extends Controller
{
    use GeneratesCorporateDocumentNumbers;
    use HandlesUploads;

    public function index()
    {
        $minutes = Minute::with('notice')->latest()->get();
        $notices = Notice::orderBy('date_of_meeting')->get();

        return view('corporate.minutes.index', [
            'minutes' => $minutes,
            'notices' => $notices,
            'nextMinutesRef' => $this->nextMinutesRef(),
        ]);
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Minutes of Meeting',
            'action' => route('minutes.store'),
            'method' => 'POST',
            'cancelRoute' => route('minutes'),
            'fields' => $this->fields(),
            'item' => new Minute([
                'minutes_ref' => $this->nextMinutesRef(),
                'date_uploaded' => now()->toDateString(),
                'uploaded_by' => auth()->user()?->name ?? '',
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data = $this->mergeNoticeData($data);
        $data['document_path'] = $this->handleUpload($request, 'document_path');
        $data['minutes_ref'] = $data['minutes_ref'] ?: $this->nextMinutesRef();
        $data = $this->filterPersistableData($data);

        Minute::create($data);

        return redirect()->route('minutes')->with('success', 'Minutes created.');
    }

    public function show(Minute $minute)
    {
        $minute->load('notice');

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
        $data = $this->mergeNoticeData($data);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $minute->document_path);
        $data = $this->filterPersistableData($data);

        $minute->update($data);

        return redirect()->route('minutes')->with('success', 'Minutes updated.');
    }

    public function approve(Request $request, Minute $minute)
    {
        abort_unless($this->userCanApprove(), 403);

        $validated = $request->validate([
            'approved_minutes_path' => [$minute->approved_minutes_path ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $this->updateExistingColumns($minute, [
            'approved_by' => auth()->user()?->name,
            'approved_minutes_path' => $this->handleUpload($request, 'approved_minutes_path', $minute->approved_minutes_path),
        ]);

        return redirect()->back()->with('success', $validated['approved_minutes_path'] ?? null ? 'Minutes approved and signed copy uploaded.' : 'Minutes approval updated.');
    }

    public function saveWorkspace(Request $request, Minute $minute)
    {
        $request->validate([
            'tentative_audio' => ['nullable', 'file', 'max:51200'],
            'remove_tentative_audio' => ['nullable', 'boolean'],
            'recording_clips' => ['nullable', 'array'],
            'recording_clips.*' => ['file', 'max:51200'],
            'sync_recording_clips' => ['nullable', 'boolean'],
            'retained_recording_clips' => ['nullable', 'array'],
            'retained_recording_clips.*' => ['string'],
            'meeting_video' => ['nullable', 'file', 'max:204800'],
            'remove_meeting_video' => ['nullable', 'boolean'],
            'script_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt', 'max:51200'],
            'remove_script_file' => ['nullable', 'boolean'],
            'recording_notes' => ['nullable', 'string'],
            'script_text' => ['nullable', 'string'],
        ]);

        $this->updateExistingColumns($minute, [
            'tentative_audio_path' => $this->handleUpload($request, 'tentative_audio', $minute->tentative_audio_path),
            'recording_clips' => $this->syncRecordingClips($request, $minute),
            'meeting_video_path' => $this->handleUpload($request, 'meeting_video', $minute->meeting_video_path),
            'script_file_path' => $this->handleUpload($request, 'script_file', $minute->script_file_path),
            'recording_notes' => $request->input('recording_notes', $minute->recording_notes),
            'script_text' => $request->input('script_text', $minute->script_text),
        ]);

        return response()->json($this->workspacePayload($minute->fresh()));
    }

    public function saveFinalRecording(Request $request, Minute $minute)
    {
        $request->validate([
            'final_audio' => ['nullable', 'file', 'max:51200'],
            'remove_final_audio' => ['nullable', 'boolean'],
        ]);

        $finalPath = $this->handleUpload($request, 'final_audio', $minute->final_audio_path);

        if (
            !$request->hasFile('final_audio')
            && !$request->boolean('remove_final_audio')
            && !$finalPath
            && $minute->tentative_audio_path
        ) {
            $finalPath = $this->duplicateUpload($minute->tentative_audio_path);
        }

        if (!$finalPath) {
            return response()->json([
                'message' => 'No tentative audio is available to save to the final preview.',
            ], 422);
        }

        $this->updateExistingColumns($minute, [
            'final_audio_path' => $finalPath,
        ]);

        return response()->json($this->workspacePayload($minute->fresh()));
    }

    public function saveFinalPreview(Request $request, Minute $minute)
    {
        $request->validate([
            'tentative_audio' => ['nullable', 'file', 'max:51200'],
            'remove_tentative_audio' => ['nullable', 'boolean'],
            'final_audio' => ['nullable', 'file', 'max:51200'],
            'remove_final_audio' => ['nullable', 'boolean'],
            'recording_clips' => ['nullable', 'array'],
            'recording_clips.*' => ['file', 'max:51200'],
            'sync_recording_clips' => ['nullable', 'boolean'],
            'retained_recording_clips' => ['nullable', 'array'],
            'retained_recording_clips.*' => ['string'],
            'meeting_video' => ['nullable', 'file', 'max:204800'],
            'remove_meeting_video' => ['nullable', 'boolean'],
            'script_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt', 'max:51200'],
            'remove_script_file' => ['nullable', 'boolean'],
            'recording_notes' => ['nullable', 'string'],
            'script_text' => ['nullable', 'string'],
        ]);

        $finalPath = $this->handleUpload($request, 'final_audio', $minute->final_audio_path);

        if (
            ! $request->hasFile('final_audio')
            && ! $request->boolean('remove_final_audio')
            && ! $finalPath
            && $minute->tentative_audio_path
        ) {
            $finalPath = $this->duplicateUpload($minute->tentative_audio_path);
        }

        $this->updateExistingColumns($minute, [
            'tentative_audio_path' => $this->handleUpload($request, 'tentative_audio', $minute->tentative_audio_path),
            'final_audio_path' => $finalPath,
            'recording_clips' => $this->syncRecordingClips($request, $minute),
            'meeting_video_path' => $this->handleUpload($request, 'meeting_video', $minute->meeting_video_path),
            'script_file_path' => $this->handleUpload($request, 'script_file', $minute->script_file_path),
            'recording_notes' => $request->input('recording_notes', $minute->recording_notes),
            'script_text' => $request->input('script_text', $minute->script_text),
        ]);

        return response()->json($this->workspacePayload($minute->fresh()));
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
            ['name' => 'governing_body', 'label' => 'Governing Body', 'type' => 'select', 'options' => $this->governingBodyOptions()],
            ['name' => 'type_of_meeting', 'label' => 'Type of Meeting', 'type' => 'select', 'options' => $this->meetingTypeOptions()],
            ['name' => 'meeting_mode', 'label' => 'Meeting Mode', 'type' => 'select', 'options' => ['In-Person', 'Virtual', 'Hybrid']],
            ['name' => 'notice_id', 'label' => 'Linked Notice', 'type' => 'text'],
            ['name' => 'notice_ref', 'label' => 'Notice Reference #', 'type' => 'text'],
            ['name' => 'date_of_meeting', 'label' => 'Date of Meeting', 'type' => 'date'],
            ['name' => 'time_started', 'label' => 'Time Started', 'type' => 'time'],
            ['name' => 'time_ended', 'label' => 'Time Ended', 'type' => 'time'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'call_link', 'label' => 'Call Link', 'type' => 'text'],
            ['name' => 'recording_notes', 'label' => 'Recording Notes', 'type' => 'text'],
            ['name' => 'script_text', 'label' => 'Script Text', 'type' => 'textarea'],
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
            'notice_id' => ['required', 'integer', 'exists:notices,id'],
            'notice_ref' => ['nullable', 'string', 'max:255'],
            'date_of_meeting' => ['nullable', 'date'],
            'time_started' => ['nullable'],
            'time_ended' => ['nullable'],
            'location' => ['nullable', 'string', 'max:255'],
            'call_link' => ['nullable', 'string', 'max:255'],
            'recording_notes' => ['nullable', 'string', 'max:255'],
            'script_text' => ['nullable', 'string'],
            'meeting_no' => ['nullable', 'string', 'max:255'],
            'chairman' => ['nullable', 'string', 'max:255'],
            'secretary' => ['nullable', 'string', 'max:255'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function mergeNoticeData(array $data): array
    {
        $notice = Notice::find($data['notice_id']);
        if (!$notice) {
            return $data;
        }

        $data['notice_ref'] = $data['notice_ref'] ?: $notice->notice_number;
        $data['governing_body'] = $data['governing_body'] ?: $notice->governing_body;
        $data['type_of_meeting'] = $data['type_of_meeting'] ?: $notice->type_of_meeting;
        $data['date_of_meeting'] = $data['date_of_meeting'] ?: optional($notice->date_of_meeting)?->toDateString();
        $data['time_started'] = $data['time_started'] ?: $notice->time_started;
        $data['location'] = $data['location'] ?: $notice->location;
        $data['meeting_no'] = $data['meeting_no'] ?: $notice->meeting_no;
        $data['chairman'] = $data['chairman'] ?: $notice->chairman;
        $data['secretary'] = $data['secretary'] ?: $notice->secretary;

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
            ->filter(fn ($value, $key) => Schema::hasColumn('minutes', $key))
            ->all();
    }

    private function updateExistingColumns(Minute $minute, array $data): void
    {
        $payload = collect($data)
            ->filter(fn ($value, $key) => Schema::hasColumn('minutes', $key))
            ->all();

        if ($payload !== []) {
            $minute->update($payload);
        }
    }

    private function userCanApprove(): bool
    {
        return auth()->check() && auth()->user()?->role === 'Admin';
    }

    private function workspacePayload(Minute $minute): array
    {
        return [
            'message' => 'Workspace files saved.',
            'tentative_audio_url' => $minute->tentative_audio_path ? route('uploads.show', ['path' => $minute->tentative_audio_path]) : null,
            'tentative_audio_download_url' => $minute->tentative_audio_path ? route('uploads.show', ['path' => $minute->tentative_audio_path, 'download' => 1]) : null,
            'tentative_audio_filename' => $minute->tentative_audio_path ? basename($minute->tentative_audio_path) : null,
            'final_audio_url' => $minute->final_audio_path ? route('uploads.show', ['path' => $minute->final_audio_path]) : null,
            'final_audio_download_url' => $minute->final_audio_path ? route('uploads.show', ['path' => $minute->final_audio_path, 'download' => 1]) : null,
            'final_audio_filename' => $minute->final_audio_path ? basename($minute->final_audio_path) : null,
            'meeting_video_url' => $minute->meeting_video_path ? route('uploads.show', ['path' => $minute->meeting_video_path]) : null,
            'meeting_video_download_url' => $minute->meeting_video_path ? route('uploads.show', ['path' => $minute->meeting_video_path, 'download' => 1]) : null,
            'meeting_video_filename' => $minute->meeting_video_path ? basename($minute->meeting_video_path) : null,
            'script_file_url' => $minute->script_file_path ? route('uploads.show', ['path' => $minute->script_file_path]) : null,
            'script_file_download_url' => $minute->script_file_path ? route('uploads.show', ['path' => $minute->script_file_path, 'download' => 1]) : null,
            'script_file_filename' => $minute->script_file_path ? basename($minute->script_file_path) : null,
            'recording_notes' => $minute->recording_notes,
            'script_text' => $minute->script_text,
            'recording_clips' => collect($minute->recording_clips ?? [])->map(fn ($path) => [
                'id' => $path,
                'url' => route('uploads.show', ['path' => $path]),
                'download_url' => route('uploads.show', ['path' => $path, 'download' => 1]),
                'filename' => basename($path),
            ])->values()->all(),
        ];
    }

    private function syncRecordingClips(Request $request, Minute $minute): array
    {
        $currentClips = collect($minute->recording_clips ?? []);
        $clips = $request->boolean('sync_recording_clips')
            ? $currentClips->filter(fn ($path) => in_array($path, $request->input('retained_recording_clips', []), true))->values()
            : $currentClips->values();

        $currentClips
            ->diff($clips)
            ->each(fn ($path) => Storage::disk('public')->delete($path));

        if ($request->hasFile('recording_clips')) {
            foreach ($request->file('recording_clips') as $clip) {
                $clips->push($this->storeUploadedFile($clip));
            }
        }

        return $clips->values()->all();
    }

    private function duplicateUpload(string $existingPath): ?string
    {
        if (!Storage::disk('public')->exists($existingPath)) {
            return null;
        }

        $copyPath = 'uploads/' . uniqid('final-audio-', true) . '/' . basename($existingPath);

        Storage::disk('public')->copy($existingPath, $copyPath);

        return $copyPath;
    }
}
