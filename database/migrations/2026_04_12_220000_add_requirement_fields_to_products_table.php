<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'requirements')) {
                $table->json('requirements')->nullable()->after('product_inclusions');
            }

            if (! Schema::hasColumn('products', 'requirement_category')) {
                $table->string('requirement_category')->nullable()->after('requirements');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'requirement_category')) {
                $table->dropColumn('requirement_category');
            }

            if (Schema::hasColumn('products', 'requirements')) {
                $table->dropColumn('requirements');
            }
        });
    }
};
