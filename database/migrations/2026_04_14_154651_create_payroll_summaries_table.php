<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('payroll_level_id')->constrained('payroll_levels')->cascadeOnDelete();
            $table->enum('computation_type', ['monthly', 'daily']);
            $table->decimal('gross_pay', 12, 2)->default(0);
            $table->decimal('total_benefits', 12, 2)->default(0);
            $table->decimal('total_allowances', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('night_differential_amount', 12, 2)->default(0);
            $table->decimal('holiday_pay_amount', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->json('breakdown_json')->nullable();
            $table->enum('status', ['generated', 'approved', 'released'])->default('generated');
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_summaries');
    }
};