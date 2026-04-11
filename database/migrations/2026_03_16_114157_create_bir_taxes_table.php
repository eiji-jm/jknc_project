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
        Schema::create('bir_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('tin')->nullable();
            $table->string('tax_payer')->nullable();
            $table->string('registering_office')->nullable();
            $table->string('registered_address')->nullable();
            $table->string('tax_types')->nullable();
            $table->string('form_type')->nullable();
            $table->string('filing_frequency')->nullable();
            $table->date('due_date')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->date('date_uploaded')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bir_taxes');
    }
};
