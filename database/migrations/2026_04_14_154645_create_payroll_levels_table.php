<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_grade_id')->constrained('salary_grades')->cascadeOnDelete();
            $table->string('level_name');
            $table->enum('computation_type', ['monthly', 'daily']);
            $table->enum('work_schedule', ['every_day', 'no_sunday', 'no_sat_sun'])->nullable();
            $table->decimal('hours_per_day', 5, 2)->default(8);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_levels');
    }
};