<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfer_book_certificates', function (Blueprint $table) {
            $table->string('status')->nullable()->after('corporate_secretary');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfer_book_certificates', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};