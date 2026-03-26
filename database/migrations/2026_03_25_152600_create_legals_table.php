<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legals', function (Blueprint $table) {
            $table->id();
            $table->string('legal_type');
            $table->string('client');
            $table->string('tin')->nullable();
            $table->date('date')->nullable();
            $table->string('document_type')->nullable();
            $table->string('document_name')->nullable();
            $table->string('document_path')->nullable();
            $table->string('user')->default('System');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legals');
    }
};