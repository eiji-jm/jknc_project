<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_book_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_book_index_id')->constrained('stock_transfer_book_indexes')->cascadeOnDelete();
            $table->string('certificate_no')->nullable();
            $table->integer('number_of_shares')->nullable();
            $table->date('date_registered')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_book_ledgers');
    }
};