<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE minutes MODIFY recording_notes LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE minutes MODIFY recording_notes VARCHAR(255) NULL');
    }
};
