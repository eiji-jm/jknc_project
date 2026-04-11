<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfer_installments', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_installments', 'par_value')) {
                $table->decimal('par_value', 12, 2)->nullable()->after('no_installments');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfer_installments', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_installments', 'par_value')) {
                $table->dropColumn('par_value');
            }
        });
    }
};
