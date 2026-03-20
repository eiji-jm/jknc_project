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
        Schema::create('stock_transfer_journals', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date')->nullable();
            $table->string('journal_no')->nullable();
            $table->string('ledger_folio')->nullable();
            $table->text('particulars')->nullable();
            $table->unsignedInteger('no_shares')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('certificate_no')->nullable();
            $table->string('shareholder')->nullable();
            $table->text('remarks')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_journals');
    }
};
