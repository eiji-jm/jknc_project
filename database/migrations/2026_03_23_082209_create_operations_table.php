<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->date('date_uploaded')->nullable();
            $table->string('user');
            $table->string('client');
            $table->string('tin')->nullable();
            $table->string('operation_type');
            $table->string('document_type');
            $table->string('status')->default('Open');
            $table->string('document_name')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};