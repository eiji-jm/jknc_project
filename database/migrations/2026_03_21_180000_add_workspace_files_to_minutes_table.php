<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minutes', function (Blueprint $table) {
            $table->string('tentative_audio_path')->nullable()->after('approved_minutes_path');
            $table->string('final_audio_path')->nullable()->after('tentative_audio_path');
            $table->string('meeting_video_path')->nullable()->after('final_audio_path');
            $table->string('script_file_path')->nullable()->after('meeting_video_path');
        });
    }

    public function down(): void
    {
        Schema::table('minutes', function (Blueprint $table) {
            $table->dropColumn([
                'tentative_audio_path',
                'final_audio_path',
                'meeting_video_path',
                'script_file_path',
            ]);
        });
    }
};
