<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'linked_service_id')) {
            return;
        }

        $orphaned = DB::table('products')
            ->whereNotNull('linked_service_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('services')
                    ->whereColumn('services.id', 'products.linked_service_id');
            })
            ->count();

        if ($orphaned > 0) {
            throw new RuntimeException("Cannot add products.linked_service_id FK: {$orphaned} row(s) reference missing services.");
        }

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('linked_service_id')
                ->references('id')
                ->on('services')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'linked_service_id')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['linked_service_id']);
        });
    }
};

