<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minutes', function (Blueprint $table) {
            $table->json('recording_clips')->nullable()->after('script_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('minutes', function (Blueprint $table) {
            $table->dropColumn('recording_clips');
        });
    }
};
