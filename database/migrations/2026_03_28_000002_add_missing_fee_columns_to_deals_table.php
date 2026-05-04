<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('deals')) {
            return;
        }

        Schema::table('deals', function (Blueprint $table) {
            if (! Schema::hasColumn('deals', 'total_service_fee')) {
                $table->decimal('total_service_fee', 12, 2)->nullable()->after('services');
            }

            if (! Schema::hasColumn('deals', 'total_product_fee')) {
                $table->decimal('total_product_fee', 12, 2)->nullable()->after('products');
            }

            if (! Schema::hasColumn('deals', 'other_fees')) {
                $table->json('other_fees')->nullable()->after('estimated_service_support_fee');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('deals')) {
            return;
        }

        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'other_fees')) {
                $table->dropColumn('other_fees');
            }

            if (Schema::hasColumn('deals', 'total_product_fee')) {
                $table->dropColumn('total_product_fee');
            }

            if (Schema::hasColumn('deals', 'total_service_fee')) {
                $table->dropColumn('total_service_fee');
            }
        });
    }
};
