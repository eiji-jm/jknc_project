<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_book_journals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_book_ledger_id');
            $table->date('entry_date')->nullable();
            $table->string('journal_no')->nullable();
            $table->string('ledger_folio')->nullable();
            $table->text('particulars')->nullable();
            $table->integer('no_shares')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('certificate_no')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('stock_transfer_book_ledger_id', 'stb_journals_ledger_fk')
                ->references('id')
                ->on('stock_transfer_book_ledgers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_book_journals');
    }
};