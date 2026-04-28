<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_grades', function (Blueprint $table) {
            $table->enum('payment_type', ['daily', 'monthly'])->default('monthly')->after('name');
            $table->decimal('monthly_basic_pay', 12, 2)->default(0)->after('payment_type');
            $table->decimal('hourly_rate', 12, 4)->default(0)->after('applicable_daily_rate');
            $table->decimal('minute_rate', 12, 6)->default(0)->after('hourly_rate');
            $table->decimal('yearly_rate', 12, 2)->default(0)->after('minute_rate');
            $table->date('date_created')->nullable()->after('yearly_rate');
            $table->string('policy_number')->nullable()->after('date_created');
            $table->string('basis_file_path')->nullable()->after('policy_number');
        });

        Schema::table('payroll_levels', function (Blueprint $table) {
            $table->string('work_schedule_label')->nullable()->after('work_schedule');
            $table->date('date_created')->nullable()->after('hours_per_day');
            $table->string('policy_number')->nullable()->after('date_created');
            $table->string('basis_file_path')->nullable()->after('policy_number');
        });

        Schema::table('payroll_benefits', function (Blueprint $table) {
            $table->foreignId('salary_grade_id')->nullable()->after('id')->constrained('salary_grades')->nullOnDelete();
            $table->foreignId('payroll_level_id')->nullable()->after('salary_grade_id')->constrained('payroll_levels')->nullOnDelete();
            $table->decimal('rate', 12, 4)->nullable()->after('type');
            $table->date('date_created')->nullable()->after('is_active');
            $table->string('policy_number')->nullable()->after('date_created');
            $table->string('basis_file_path')->nullable()->after('policy_number');
        });

        Schema::table('payroll_allowances', function (Blueprint $table) {
            $table->foreignId('salary_grade_id')->nullable()->after('id')->constrained('salary_grades')->nullOnDelete();
            $table->foreignId('payroll_level_id')->nullable()->after('salary_grade_id')->constrained('payroll_levels')->nullOnDelete();
            $table->decimal('rate', 12, 4)->nullable()->after('type');
            $table->date('date_created')->nullable()->after('is_active');
            $table->string('policy_number')->nullable()->after('date_created');
            $table->string('basis_file_path')->nullable()->after('policy_number');
        });

        Schema::table('payroll_deductions', function (Blueprint $table) {
            $table->foreignId('salary_grade_id')->nullable()->after('id')->constrained('salary_grades')->nullOnDelete();
            $table->foreignId('payroll_level_id')->nullable()->after('salary_grade_id')->constrained('payroll_levels')->nullOnDelete();
            $table->decimal('rate', 12, 4)->nullable()->after('type');
            $table->date('date_created')->nullable()->after('is_active');
            $table->string('policy_number')->nullable()->after('date_created');
            $table->string('basis_file_path')->nullable()->after('policy_number');
        });

        Schema::table('payroll_holidays', function (Blueprint $table) {
            $table->foreignId('salary_grade_id')->nullable()->after('id')->constrained('salary_grades')->nullOnDelete();
            $table->foreignId('payroll_level_id')->nullable()->after('salary_grade_id')->constrained('payroll_levels')->nullOnDelete();
            $table->string('holiday_category')->nullable()->after('holiday_type');
            $table->decimal('percentage', 12, 4)->default(0)->after('holiday_category');
            $table->decimal('holiday_value', 12, 2)->default(0)->after('percentage');
            $table->date('date_created')->nullable()->after('holiday_value');
            $table->string('policy_number')->nullable()->after('date_created');
            $table->string('basis_file_path')->nullable()->after('policy_number');
        });

        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->date('payroll_start')->nullable()->after('period_end');
            $table->date('payroll_end')->nullable()->after('payroll_start');
            $table->date('dispute_start')->nullable()->after('pay_date');
            $table->date('dispute_end')->nullable()->after('dispute_start');
            $table->date('date_created')->nullable()->after('dispute_end');
            $table->string('policy_number')->nullable()->after('date_created');
            $table->string('basis_file_path')->nullable()->after('policy_number');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->dropColumn([
                'payroll_start',
                'payroll_end',
                'dispute_start',
                'dispute_end',
                'date_created',
                'policy_number',
                'basis_file_path',
            ]);
        });

        Schema::table('payroll_holidays', function (Blueprint $table) {
            $table->dropConstrainedForeignId('salary_grade_id');
            $table->dropConstrainedForeignId('payroll_level_id');
            $table->dropColumn([
                'holiday_category',
                'percentage',
                'holiday_value',
                'date_created',
                'policy_number',
                'basis_file_path',
            ]);
        });

        Schema::table('payroll_deductions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('salary_grade_id');
            $table->dropConstrainedForeignId('payroll_level_id');
            $table->dropColumn([
                'rate',
                'date_created',
                'policy_number',
                'basis_file_path',
            ]);
        });

        Schema::table('payroll_allowances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('salary_grade_id');
            $table->dropConstrainedForeignId('payroll_level_id');
            $table->dropColumn([
                'rate',
                'date_created',
                'policy_number',
                'basis_file_path',
            ]);
        });

        Schema::table('payroll_benefits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('salary_grade_id');
            $table->dropConstrainedForeignId('payroll_level_id');
            $table->dropColumn([
                'rate',
                'date_created',
                'policy_number',
                'basis_file_path',
            ]);
        });

        Schema::table('payroll_levels', function (Blueprint $table) {
            $table->dropColumn([
                'work_schedule_label',
                'date_created',
                'policy_number',
                'basis_file_path',
            ]);
        });

        Schema::table('salary_grades', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type',
                'monthly_basic_pay',
                'hourly_rate',
                'minute_rate',
                'yearly_rate',
                'date_created',
                'policy_number',
                'basis_file_path',
            ]);
        });
    }
};
