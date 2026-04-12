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
            if (! Schema::hasColumn('products', 'linked_service_ids')) {
                $table->json('linked_service_ids')->nullable()->after('linked_service_id');
            }
        });

        if (Schema::hasColumn('products', 'linked_service_id') && Schema::hasColumn('products', 'linked_service_ids')) {
            DB::table('products')
                ->whereNotNull('linked_service_id')
                ->orderBy('id')
                ->chunkById(100, function ($products): void {
                    foreach ($products as $product) {
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update([
                                'linked_service_ids' => json_encode([(int) $product->linked_service_id]),
                            ]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'linked_service_ids')) {
                $table->dropColumn('linked_service_ids');
            }
        });
    }
};
