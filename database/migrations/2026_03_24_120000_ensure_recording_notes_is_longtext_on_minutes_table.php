<?php

use Illuminate\Database\Migrations\Migration;

// This migration is a no-op. The recording_notes column was already
// converted to LONGTEXT in 2026_03_21_201000_expand_recording_notes_on_minutes_table.php
return new class extends Migration
{
    public function up(): void
    {
        // Already handled by an earlier migration
    }

    public function down(): void
    {
        // Nothing to revert
    }
};
