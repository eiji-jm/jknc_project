<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correspondences', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->date('uploaded_date');
            $table->string('user');
            $table->string('client');
            $table->string('tin')->nullable();
            $table->string('subject');
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('department')->nullable();
            $table->text('details')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->date('deadline')->nullable();
            $table->string('sent_via')->default('Email');
            $table->string('status')->default('Open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correspondences');
    }
};