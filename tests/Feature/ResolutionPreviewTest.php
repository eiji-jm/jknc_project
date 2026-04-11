<?php

use App\Models\Resolution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('does not render the attached draft iframe when the saved resolution draft file is missing', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $resolution = Resolution::create([
        'resolution_no' => 'RES-2024-001',
        'date_of_meeting' => now()->toDateString(),
        'governing_body' => 'Board of Directors',
        'type_of_meeting' => 'Regular',
        'board_resolution' => 'Approval of Budget',
        'resolution_body' => '<p>Approved.</p>',
        'chairman' => 'John Smith',
        'secretary' => 'Jane Doe',
        'draft_file_path' => 'uploads/resolutions/missing-draft.pdf',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('resolutions.preview', $resolution));

    $response
        ->assertOk()
        ->assertSee('Resolution Preview')
        ->assertDontSee('Attached Draft PDF')
        ->assertDontSee(route('uploads.show', ['path' => 'uploads/resolutions/missing-draft.pdf']), false);
});
