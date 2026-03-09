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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('owner')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('duration')->nullable();
            $table->string('location')->nullable();
            $table->integer('attendees')->default(0);
            $table->string('status')->default('upcoming');
            $table->text('description')->nullable();
            $table->boolean('has_video')->default(false);
            $table->boolean('has_audio')->default(false);
            $table->boolean('has_transcript')->default(false);
            $table->boolean('has_minutes')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
