<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('ubos', function (Blueprint $table) {
        $table->id();
        $table->string('complete_name');
        $table->text('address');
        $table->string('nationality');
        $table->date('date_of_birth');
        $table->string('tax_identification_no');
        $table->integer('ownership_percentage');
        $table->string('ownership_type'); // Direct / Indirect
        $table->string('category');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubos');
    }
};
