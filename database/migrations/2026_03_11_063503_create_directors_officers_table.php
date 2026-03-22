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
        Schema::create('directors_officers', function (Blueprint $table) {

    $table->id();
    $table->foreignId('gis_id')->constrained('gis_records')->cascadeOnDelete();

    $table->string('officer_name');
    $table->string('address');

    $table->string('gender');
    $table->string('nationality');

    $table->boolean('incr');
    $table->boolean('stockholder');

    $table->string('board');
    $table->string('officer_type');

    $table->string('committee');

    $table->string('tin');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directors_officers');
    }
};
