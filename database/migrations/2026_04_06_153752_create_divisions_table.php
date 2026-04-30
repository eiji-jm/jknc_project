<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('division_name');
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('address_id')->constrained('organizational_addresses')->cascadeOnDelete();
            $table->string('division_head');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};