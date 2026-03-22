<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permits', function (Blueprint $table) {
            $table->id();
            $table->string('permit_type');
            $table->string('permit_number')->unique();
            $table->date('date_of_registration')->nullable();
            $table->date('approved_date_of_registration')->nullable();
            $table->date('expiration_date_of_registration')->nullable();
            $table->string('user');
            $table->string('tin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permits');
    }
};