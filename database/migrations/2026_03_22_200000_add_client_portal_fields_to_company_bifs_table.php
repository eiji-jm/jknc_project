<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_bifs', function (Blueprint $table) {
            if (! Schema::hasColumn('company_bifs', 'client_access_token')) {
                $table->string('client_access_token', 80)->nullable()->unique()->after('status');
            }

            if (! Schema::hasColumn('company_bifs', 'client_access_expires_at')) {
                $table->timestamp('client_access_expires_at')->nullable()->after('client_access_token');
            }

            if (! Schema::hasColumn('company_bifs', 'client_form_sent_to_email')) {
                $table->string('client_form_sent_to_email')->nullable()->after('client_access_expires_at');
            }

            if (! Schema::hasColumn('company_bifs', 'client_form_sent_at')) {
                $table->timestamp('client_form_sent_at')->nullable()->after('client_form_sent_to_email');
            }

            if (! Schema::hasColumn('company_bifs', 'client_submitted_at')) {
                $table->timestamp('client_submitted_at')->nullable()->after('client_form_sent_at');
            }

            if (! Schema::hasColumn('company_bifs', 'last_submission_source')) {
                $table->string('last_submission_source', 30)->nullable()->after('client_submitted_at');
            }

            if (! Schema::hasColumn('company_bifs', 'last_manual_updated_at')) {
                $table->timestamp('last_manual_updated_at')->nullable()->after('last_submission_source');
            }

            if (! Schema::hasColumn('company_bifs', 'last_manual_updated_by_name')) {
                $table->string('last_manual_updated_by_name')->nullable()->after('last_manual_updated_at');
            }

            if (! Schema::hasColumn('company_bifs', 'client_requirement_documents')) {
                $table->json('client_requirement_documents')->nullable()->after('ubos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('company_bifs', function (Blueprint $table) {
            $columns = [
                'client_access_token',
                'client_access_expires_at',
                'client_form_sent_to_email',
                'client_form_sent_at',
                'client_submitted_at',
                'last_submission_source',
                'last_manual_updated_at',
                'last_manual_updated_by_name',
                'client_requirement_documents',
            ];

            $existing = array_filter($columns, fn (string $column) => Schema::hasColumn('company_bifs', $column));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
