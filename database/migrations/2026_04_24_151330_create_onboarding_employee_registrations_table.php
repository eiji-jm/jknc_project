<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_employee_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('employee_id')->unique();
            $table->string('department')->nullable();
            $table->date('start_date')->nullable();
            $table->string('work_email')->nullable();
            $table->string('manager')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_employee_registrations');
    }
};