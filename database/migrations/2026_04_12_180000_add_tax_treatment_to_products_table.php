<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'tax_treatment')) {
                $table->string('tax_treatment')->nullable()->after('tax_type');
            }
        });

        if (Schema::hasColumn('products', 'tax_treatment')) {
            DB::table('products')
                ->whereNull('tax_treatment')
                ->update(['tax_treatment' => 'Tax Exclusive']);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'tax_treatment')) {
                $table->dropColumn('tax_treatment');
            }
        });
    }
};
