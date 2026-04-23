<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidate_assessments', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('position');
            $table->string('cv_path')->nullable()->after('photo_path');
            $table->string('cover_letter_path')->nullable()->after('cv_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_assessments', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'cv_path', 'cover_letter_path']);
        });
    }
};
