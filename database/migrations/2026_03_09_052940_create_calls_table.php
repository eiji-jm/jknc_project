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
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->string('contact');
            $table->string('type')->default('Outbound');
            $table->string('start_time')->nullable();
            $table->string('start_hour')->nullable();
            $table->string('duration')->nullable();
            $table->string('related_to')->nullable();
            $table->string('owner')->nullable();
            $table->boolean('completed')->default(false);
            $table->string('purpose')->nullable();
            $table->string('agenda')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
