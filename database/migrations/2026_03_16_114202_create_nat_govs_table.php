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
        Schema::create('nat_govs', function (Blueprint $table) {
            $table->id();
            $table->string('client')->nullable();
            $table->string('tin')->nullable();
            $table->string('agency')->nullable();
            $table->string('registration_status')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('nat_govs');
    }
};
