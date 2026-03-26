<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accountings', function (Blueprint $table) {
            $table->id();
            $table->string('statement_type'); // PNL, Balance Sheet, Cash Flow, Income Statement, AFS
            $table->string('client');
            $table->string('tin')->nullable();
            $table->date('date')->nullable();
            $table->string('user');
            $table->string('status')->default('Open');
            $table->string('document_name')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accountings');
    }
};