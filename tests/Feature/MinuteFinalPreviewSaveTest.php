<?php

use App\Models\Minute;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('saves final preview attachments alongside long minutes content', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'Admin',
    ]);

    $notice = Notice::create([
        'notice_number' => 'NOTICE-2026-001',
        'date_of_notice' => now()->toDateString(),
        'governing_body' => 'Board of Directors',
        'type_of_meeting' => 'Special',
        'date_of_meeting' => now()->toDateString(),
        'time_started' => '09:00:00',
        'location' => 'Main Office',
        'meeting_no' => '1',
        'chairman' => 'Jane Chair',
        'secretary' => 'Sam Sec',
        'uploaded_by' => 'Admin User',
    ]);

    $minute = Minute::create([
        'minutes_ref' => 'MIN-2026-001',
        'notice_id' => $notice->id,
        'notice_ref' => $notice->notice_number,
        'governing_body' => $notice->governing_body,
        'type_of_meeting' => $notice->type_of_meeting,
        'date_of_meeting' => $notice->date_of_meeting,
        'location' => $notice->location,
        'recording_notes' => null,
    ]);

    $recording = UploadedFile::fake()->create('recording.webm', 256, 'audio/webm');
    $clip = UploadedFile::fake()->create('recording-clip.webm', 128, 'audio/webm');
    $video = UploadedFile::fake()->create('meeting.mp4', 512, 'video/mp4');
    $script = UploadedFile::fake()->createWithContent('meeting-script.pdf', '%PDF-1.4 test');
    $longMinutesHtml = str_repeat('<p>Resolved and discussed in detail.</p>', 40);

    $response = $this
        ->actingAs($user)
        ->post(route('minutes.final-save', $minute), [
            'tentative_audio' => $recording,
            'final_audio' => UploadedFile::fake()->create('final-recording.webm', 256, 'audio/webm'),
            'recording_clips' => [$clip],
            'sync_recording_clips' => '1',
            'meeting_video' => $video,
            'script_file' => $script,
            'recording_notes' => $longMinutesHtml,
            'script_text' => str_repeat('Speaker notes. ', 20),
        ], [
            'Accept' => 'application/json',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('recording_notes', $longMinutesHtml);

    $minute->refresh();

    expect($minute->recording_notes)->toBe($longMinutesHtml);
    expect($minute->tentative_audio_path)->not->toBeNull();
    expect($minute->final_audio_path)->not->toBeNull();
    expect($minute->meeting_video_path)->not->toBeNull();
    expect($minute->script_file_path)->not->toBeNull();
    expect($minute->recording_clips)->toHaveCount(1);

    Storage::disk('public')->assertExists($minute->tentative_audio_path);
    Storage::disk('public')->assertExists($minute->final_audio_path);
    Storage::disk('public')->assertExists($minute->meeting_video_path);
    Storage::disk('public')->assertExists($minute->script_file_path);
    Storage::disk('public')->assertExists($minute->recording_clips[0]);
});
