<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bankings', function (Blueprint $table) {
            $table->id();
            $table->date('date_uploaded')->nullable();
            $table->string('user');
            $table->string('client');
            $table->string('tin')->nullable();
            $table->string('bank');
            $table->string('bank_doc');
            $table->string('status')->default('Open');
            $table->string('document_name')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bankings');
    }
};