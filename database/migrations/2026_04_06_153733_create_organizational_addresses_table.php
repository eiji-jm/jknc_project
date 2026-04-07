<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizational_addresses', function (Blueprint $table) {
            $table->id();
            $table->text('business_address');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizational_addresses');
    }
};