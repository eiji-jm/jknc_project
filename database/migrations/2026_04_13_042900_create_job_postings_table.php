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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->unique();
            $table->string('position');
            $table->string('employment_type');
            $table->string('location')->nullable();
            $table->string('salary_range')->nullable();
            $table->text('job_description')->nullable();
            $table->text('requirements')->nullable();
            $table->date('posted_date');
            $table->string('status')->default('Open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
