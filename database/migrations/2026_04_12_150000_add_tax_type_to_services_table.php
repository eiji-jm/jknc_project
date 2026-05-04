<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('services')) {
            return;
        }

        if (! Schema::hasColumn('services', 'tax_type')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->string('tax_type', 30)->default('Tax Exclusive')->after('cost_of_service');
            });
        }

        DB::table('services')
            ->whereNull('tax_type')
            ->update(['tax_type' => 'Tax Exclusive']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('services') || ! Schema::hasColumn('services', 'tax_type')) {
            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn('tax_type');
        });
    }
};
