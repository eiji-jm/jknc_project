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
        Schema::create('subscribed_capitals', function (Blueprint $table) {

    $table->id();
    $table->foreignId('gis_id')->constrained('gis_records')->cascadeOnDelete();

    $table->string('nationality');
    $table->integer('no_of_stockholders');

    $table->string('share_type');
    $table->integer('number_of_shares');

    $table->decimal('par_value',12,2);
    $table->decimal('amount',15,2);

    $table->decimal('ownership_percentage',5,2);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribed_capitals');
    }
};
