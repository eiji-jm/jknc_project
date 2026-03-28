<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_book_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_book_ledger_id');
            $table->string('stock_number')->nullable();
            $table->string('subscriber')->nullable();
            $table->date('installment_date')->nullable();
            $table->integer('no_shares')->nullable();
            $table->integer('no_installments')->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            $table->decimal('installment_amount', 15, 2)->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('stock_transfer_book_ledger_id', 'stb_installments_ledger_fk')
                ->references('id')
                ->on('stock_transfer_book_ledgers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_book_installments');
    }
};