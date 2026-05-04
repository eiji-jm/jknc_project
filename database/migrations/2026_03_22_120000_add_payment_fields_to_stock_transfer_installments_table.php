<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfer_installments', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_installments', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('installment_amount');
            }

            if (!Schema::hasColumn('stock_transfer_installments', 'payment_amount')) {
                $table->decimal('payment_amount', 12, 2)->nullable()->after('payment_date');
            }

            if (!Schema::hasColumn('stock_transfer_installments', 'payment_remarks')) {
                $table->text('payment_remarks')->nullable()->after('payment_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfer_installments', function (Blueprint $table) {
            foreach (['payment_remarks', 'payment_amount', 'payment_date'] as $column) {
                if (Schema::hasColumn('stock_transfer_installments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
