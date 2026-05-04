<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deal_proposals', function (Blueprint $table): void {
            if (! Schema::hasColumn('deal_proposals', 'invoice_status')) {
                $table->string('invoice_status')->nullable()->after('quotation_approved_by_name');
            }

            if (! Schema::hasColumn('deal_proposals', 'invoice_file_path')) {
                $table->string('invoice_file_path')->nullable()->after('invoice_status');
            }

            if (! Schema::hasColumn('deal_proposals', 'invoice_generated_at')) {
                $table->timestamp('invoice_generated_at')->nullable()->after('invoice_file_path');
            }

            if (! Schema::hasColumn('deal_proposals', 'invoice_uploaded_at')) {
                $table->timestamp('invoice_uploaded_at')->nullable()->after('invoice_generated_at');
            }

            if (! Schema::hasColumn('deal_proposals', 'payment_confirmed_at')) {
                $table->timestamp('payment_confirmed_at')->nullable()->after('invoice_uploaded_at');
            }

            if (! Schema::hasColumn('deal_proposals', 'payment_confirmed_by_name')) {
                $table->string('payment_confirmed_by_name')->nullable()->after('payment_confirmed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deal_proposals', function (Blueprint $table): void {
            $columns = [
                'payment_confirmed_by_name',
                'payment_confirmed_at',
                'invoice_uploaded_at',
                'invoice_generated_at',
                'invoice_file_path',
                'invoice_status',
            ];

            $existing = array_filter($columns, fn (string $column): bool => Schema::hasColumn('deal_proposals', $column));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
