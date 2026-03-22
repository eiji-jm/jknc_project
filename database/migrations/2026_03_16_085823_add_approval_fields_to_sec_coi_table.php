<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sec_coi', function (Blueprint $table) {
            $table->string('approval_status')->default('Pending')->after('file_path');
            $table->unsignedBigInteger('submitted_by')->nullable()->after('approval_status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('submitted_by');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('review_note')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('sec_coi', function (Blueprint $table) {
            $table->dropColumn([
                'approval_status',
                'submitted_by',
                'approved_by',
                'approved_at',
                'review_note',
            ]);
        });
    }
};