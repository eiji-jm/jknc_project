<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_transfer_installments', function (Blueprint $table) {
            $table->id();
            $table->string('stock_number')->nullable();
            $table->string('subscriber')->nullable();
            $table->date('installment_date')->nullable();
            $table->unsignedInteger('no_shares')->nullable();
            $table->unsignedInteger('no_installments')->nullable();
            $table->decimal('total_value', 12, 2)->nullable();
            $table->decimal('installment_amount', 12, 2)->nullable();
            $table->string('status')->nullable();
            $table->json('schedule')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_installments');
    }
};
