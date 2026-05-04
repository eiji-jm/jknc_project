<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('deals') || Schema::hasColumn('deals', 'deal_discount')) {
            return;
        }

        Schema::table('deals', function (Blueprint $table) {
            $table->decimal('deal_discount', 12, 2)->nullable()->after('total_product_fee');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('deals') || ! Schema::hasColumn('deals', 'deal_discount')) {
            return;
        }

        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('deal_discount');
        });
    }
};
