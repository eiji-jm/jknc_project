<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deal_proposals', function (Blueprint $table): void {
            if (! Schema::hasColumn('deal_proposals', 'quotation_status')) {
                $table->string('quotation_status')->nullable()->after('client_approval_note');
            }

            if (! Schema::hasColumn('deal_proposals', 'quotation_finance_file_path')) {
                $table->string('quotation_finance_file_path')->nullable()->after('quotation_status');
            }

            if (! Schema::hasColumn('deal_proposals', 'quotation_client_file_path')) {
                $table->string('quotation_client_file_path')->nullable()->after('quotation_finance_file_path');
            }

            if (! Schema::hasColumn('deal_proposals', 'quotation_finance_started_at')) {
                $table->timestamp('quotation_finance_started_at')->nullable()->after('quotation_client_file_path');
            }

            if (! Schema::hasColumn('deal_proposals', 'quotation_approved_at')) {
                $table->timestamp('quotation_approved_at')->nullable()->after('quotation_finance_started_at');
            }

            if (! Schema::hasColumn('deal_proposals', 'quotation_approved_by_name')) {
                $table->string('quotation_approved_by_name')->nullable()->after('quotation_approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deal_proposals', function (Blueprint $table): void {
            $columns = [
                'quotation_approved_by_name',
                'quotation_approved_at',
                'quotation_finance_started_at',
                'quotation_client_file_path',
                'quotation_finance_file_path',
                'quotation_status',
            ];

            $existing = array_filter($columns, fn (string $column): bool => Schema::hasColumn('deal_proposals', $column));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
