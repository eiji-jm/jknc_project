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
        Schema::create('stockholders', function (Blueprint $table) {

    $table->id();
    $table->foreignId('gis_id')->constrained('gis_records')->cascadeOnDelete();

    $table->string('stockholder_name');
    $table->string('address');

    $table->string('gender');
    $table->string('nationality');

    $table->boolean('incr');

    $table->string('share_type');

    $table->integer('shares');
    $table->decimal('amount',15,2);

    $table->decimal('ownership_percentage',5,2);

    $table->decimal('amount_paid',15,2);

    $table->string('tin');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockholders');
    }
};
