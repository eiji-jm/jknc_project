<?php

use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('serves uploaded files even when the path includes the legacy storage prefix', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    Storage::disk('public')->put('uploads/notices/notice-2026-012.pdf', '%PDF-1.4 notice');

    $response = $this
        ->actingAs($user)
        ->get(route('uploads.show', ['path' => 'storage/uploads/notices/notice-2026-012.pdf']));

    $response
        ->assertOk()
        ->assertHeader('content-disposition', 'inline; filename="notice-2026-012.pdf"');
});

it('falls back to the generated notice preview when the stored pdf is missing', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $notice = Notice::create([
        'notice_number' => 'NOTICE-2026-015',
        'date_of_notice' => now()->toDateString(),
        'governing_body' => 'Stockholders',
        'type_of_meeting' => 'Regular',
        'date_of_meeting' => now()->addWeek()->toDateString(),
        'time_started' => '09:00:00',
        'location' => 'Main Office',
        'meeting_no' => '15',
        'chairman' => 'Jane Chair',
        'secretary' => 'Sam Sec',
        'uploaded_by' => 'Admin User',
        'body_html' => '<p>Agenda item one.</p>',
        'document_path' => 'uploads/notices/missing-notice.pdf',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('notices.preview', $notice));

    $response
        ->assertOk()
        ->assertSee('data:text/html;charset=UTF-8,', false)
        ->assertDontSee(route('uploads.show', ['path' => 'uploads/notices/missing-notice.pdf']), false);
});
