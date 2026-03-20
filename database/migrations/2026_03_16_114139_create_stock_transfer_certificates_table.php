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
        Schema::create('stock_transfer_certificates', function (Blueprint $table) {
            $table->id();
            $table->date('date_uploaded')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->string('corporation_name')->nullable();
            $table->string('company_reg_no')->nullable();
            $table->string('stock_number')->nullable();
            $table->string('stockholder_name')->nullable();
            $table->decimal('par_value', 12, 2)->nullable();
            $table->unsignedInteger('number')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('amount_in_words')->nullable();
            $table->date('date_issued')->nullable();
            $table->string('president')->nullable();
            $table->string('corporate_secretary')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_certificates');
    }
};
