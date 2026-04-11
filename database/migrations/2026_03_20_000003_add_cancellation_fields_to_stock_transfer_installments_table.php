<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfer_installments', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_installments', 'cancellation_date')) {
                $table->date('cancellation_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('stock_transfer_installments', 'cancellation_effective_date')) {
                $table->date('cancellation_effective_date')->nullable()->after('cancellation_date');
            }
            if (!Schema::hasColumn('stock_transfer_installments', 'cancellation_reason')) {
                $table->string('cancellation_reason')->nullable()->after('cancellation_effective_date');
            }
            if (!Schema::hasColumn('stock_transfer_installments', 'cancellation_types')) {
                $table->json('cancellation_types')->nullable()->after('cancellation_reason');
            }
            if (!Schema::hasColumn('stock_transfer_installments', 'cancellation_other_reason')) {
                $table->string('cancellation_other_reason')->nullable()->after('cancellation_types');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfer_installments', function (Blueprint $table) {
            foreach ([
                'cancellation_other_reason',
                'cancellation_types',
                'cancellation_reason',
                'cancellation_effective_date',
                'cancellation_date',
            ] as $column) {
                if (Schema::hasColumn('stock_transfer_installments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
