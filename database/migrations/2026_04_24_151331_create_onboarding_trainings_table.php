<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_trainings', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('program');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('trainer')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('Scheduled');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_trainings');
    }
};