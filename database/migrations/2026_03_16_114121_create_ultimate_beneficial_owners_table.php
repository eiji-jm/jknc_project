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
        Schema::create('ultimate_beneficial_owners', function (Blueprint $table) {
            $table->id();
            $table->string('complete_name');
            $table->string('email')->nullable();
            $table->string('residential_address')->nullable();
            $table->string('nationality')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('tax_identification_no')->nullable();
            $table->decimal('ownership_percentage', 5, 2)->nullable();
            $table->string('ownership_type')->nullable();
            $table->string('ownership_category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ultimate_beneficial_owners');
    }
};
