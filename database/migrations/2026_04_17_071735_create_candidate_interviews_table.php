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
        Schema::create('candidate_interviews', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('round');
            $table->string('interviewer');
            $table->date('interview_date');
            $table->string('status')->default('Scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_interviews');
    }
};
