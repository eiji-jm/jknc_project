<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_sow_reports', function (Blueprint $table) {
            $table->string('client_access_token')->nullable()->after('internal_approval');
            $table->dateTime('client_access_expires_at')->nullable()->after('client_access_token');
            $table->string('client_form_sent_to_email')->nullable()->after('client_access_expires_at');
            $table->dateTime('client_form_sent_at')->nullable()->after('client_form_sent_to_email');
            $table->string('client_response_status')->nullable()->after('client_form_sent_at');
            $table->dateTime('client_approved_at')->nullable()->after('client_response_status');
            $table->string('client_approved_name')->nullable()->after('client_approved_at');
            $table->text('client_response_notes')->nullable()->after('client_approved_name');
            $table->string('client_attachment_path')->nullable()->after('client_response_notes');
        });
    }

    public function down(): void
    {
        Schema::table('project_sow_reports', function (Blueprint $table) {
            $table->dropColumn([
                'client_access_token',
                'client_access_expires_at',
                'client_form_sent_to_email',
                'client_form_sent_at',
                'client_response_status',
                'client_approved_at',
                'client_approved_name',
                'client_response_notes',
                'client_attachment_path',
            ]);
        });
    }
};
