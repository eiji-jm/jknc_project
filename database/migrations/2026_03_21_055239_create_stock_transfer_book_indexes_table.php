<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_book_indexes', function (Blueprint $table) {
            $table->id();
            $table->string('family_name');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('nationality')->nullable();
            $table->text('current_address')->nullable();
            $table->string('tin')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_book_indexes');
    }
};