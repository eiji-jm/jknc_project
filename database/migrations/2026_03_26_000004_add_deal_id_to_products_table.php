<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'deal_id')) {
                $table->foreignId('deal_id')
                    ->nullable()
                    ->after('linked_service_id')
                    ->constrained('deals')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'deal_id')) {
                $table->dropConstrainedForeignId('deal_id');
            }
        });
    }
};

