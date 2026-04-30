<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deal_proposals', function (Blueprint $table): void {
            if (! Schema::hasColumn('deal_proposals', 'status')) {
                $table->string('status')->default('draft')->after('document_html');
            }

            if (! Schema::hasColumn('deal_proposals', 'client_access_token')) {
                $table->string('client_access_token')->nullable()->unique()->after('status');
            }

            if (! Schema::hasColumn('deal_proposals', 'client_access_expires_at')) {
                $table->timestamp('client_access_expires_at')->nullable()->after('client_access_token');
            }

            if (! Schema::hasColumn('deal_proposals', 'client_form_sent_to_email')) {
                $table->string('client_form_sent_to_email')->nullable()->after('client_access_expires_at');
            }

            if (! Schema::hasColumn('deal_proposals', 'client_form_sent_at')) {
                $table->timestamp('client_form_sent_at')->nullable()->after('client_form_sent_to_email');
            }

            if (! Schema::hasColumn('deal_proposals', 'client_approved_at')) {
                $table->timestamp('client_approved_at')->nullable()->after('client_form_sent_at');
            }

            if (! Schema::hasColumn('deal_proposals', 'client_approved_by_name')) {
                $table->string('client_approved_by_name')->nullable()->after('client_approved_at');
            }

            if (! Schema::hasColumn('deal_proposals', 'client_approval_note')) {
                $table->text('client_approval_note')->nullable()->after('client_approved_by_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deal_proposals', function (Blueprint $table): void {
            if (Schema::hasColumn('deal_proposals', 'client_access_token')) {
                $table->dropUnique(['client_access_token']);
            }

            $columns = [
                'client_approval_note',
                'client_approved_by_name',
                'client_approved_at',
                'client_form_sent_at',
                'client_form_sent_to_email',
                'client_access_expires_at',
                'client_access_token',
                'status',
            ];

            $existing = array_filter($columns, fn (string $column): bool => Schema::hasColumn('deal_proposals', $column));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
