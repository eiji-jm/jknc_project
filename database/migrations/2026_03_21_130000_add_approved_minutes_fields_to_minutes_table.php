<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minutes', function (Blueprint $table) {
            $table->string('approved_by')->nullable()->after('uploaded_by');
            $table->string('approved_minutes_path')->nullable()->after('document_path');
        });
    }

    public function down(): void
    {
        Schema::table('minutes', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_minutes_path']);
        });
    }
};
