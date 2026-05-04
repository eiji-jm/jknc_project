<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_bifs', function (Blueprint $table) {
            if (! Schema::hasColumn('company_bifs', 'change_request_payload')) {
                $table->json('change_request_payload')->nullable()->after('client_requirement_documents');
            }

            if (! Schema::hasColumn('company_bifs', 'change_request_status')) {
                $table->string('change_request_status', 30)->nullable()->index()->after('change_request_payload');
            }

            if (! Schema::hasColumn('company_bifs', 'change_request_note')) {
                $table->text('change_request_note')->nullable()->after('change_request_status');
            }

            if (! Schema::hasColumn('company_bifs', 'change_requested_at')) {
                $table->timestamp('change_requested_at')->nullable()->after('change_request_note');
            }

            if (! Schema::hasColumn('company_bifs', 'change_requested_by_name')) {
                $table->string('change_requested_by_name')->nullable()->after('change_requested_at');
            }

            if (! Schema::hasColumn('company_bifs', 'change_reviewed_at')) {
                $table->timestamp('change_reviewed_at')->nullable()->after('change_requested_by_name');
            }

            if (! Schema::hasColumn('company_bifs', 'change_reviewed_by_name')) {
                $table->string('change_reviewed_by_name')->nullable()->after('change_reviewed_at');
            }

            if (! Schema::hasColumn('company_bifs', 'change_rejection_reason')) {
                $table->text('change_rejection_reason')->nullable()->after('change_reviewed_by_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('company_bifs', function (Blueprint $table) {
            $columns = [
                'change_request_payload',
                'change_request_status',
                'change_request_note',
                'change_requested_at',
                'change_requested_by_name',
                'change_reviewed_at',
                'change_reviewed_by_name',
                'change_rejection_reason',
            ];

            $existing = array_filter($columns, fn (string $column) => Schema::hasColumn('company_bifs', $column));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
