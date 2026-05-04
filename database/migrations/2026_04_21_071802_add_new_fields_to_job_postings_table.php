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
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('related_mrf_no')->nullable()->after('job_id');
            $table->date('date_opened')->nullable()->after('related_mrf_no');
            $table->string('hiring_status')->nullable()->after('date_opened');
            $table->string('company_name')->nullable()->after('hiring_status');
            $table->string('office_branch_site')->nullable()->after('company_name');
            $table->string('department_unit')->nullable()->after('office_branch_site');
            $table->string('hiring_manager')->nullable()->after('department_unit');
            $table->string('department_superior')->nullable()->after('hiring_manager');
            $table->integer('no_of_vacancies')->nullable()->after('position');
            $table->string('position_level')->nullable()->after('no_of_vacancies');
            $table->string('reports_to')->nullable()->after('employment_type');
            $table->decimal('min_salary_offer', 15, 2)->nullable()->after('salary_range');
            $table->decimal('max_salary_offer', 15, 2)->nullable()->after('min_salary_offer');
            $table->string('salary_grade')->nullable()->after('max_salary_offer');
            $table->string('applicable_region')->default('Central Visayas')->after('salary_grade');
            $table->string('applicable_area')->nullable()->after('applicable_region');
            $table->decimal('current_daily_min_wage', 15, 2)->nullable()->after('applicable_area');
            $table->decimal('monthly_equivalent', 15, 2)->nullable()->after('current_daily_min_wage');
            $table->json('wage_compliance')->nullable()->after('monthly_equivalent');
            $table->json('benefits_package')->nullable()->after('wage_compliance');
            $table->json('work_schedule')->nullable()->after('benefits_package');
            $table->string('rest_days')->nullable()->after('work_schedule');
            $table->string('education_req')->nullable()->after('requirements');
            $table->string('experience_req')->nullable()->after('education_req');
            $table->string('skills_req')->nullable()->after('experience_req');
            $table->string('licenses_req')->nullable()->after('skills_req');
            $table->text('preferred_qualifications')->nullable()->after('licenses_req');
            $table->text('duties_responsibilities')->nullable()->after('job_description');
            $table->json('recruitment_channels')->nullable()->after('duties_responsibilities');
            $table->json('screening_flow')->nullable()->after('recruitment_channels');
            $table->date('date_needed')->nullable()->after('screening_flow');
            $table->date('posting_start_date')->nullable()->after('date_needed');
            $table->date('target_hire_date')->nullable()->after('posting_start_date');
            $table->json('human_capital_approval')->nullable();
            $table->json('hiring_manager_approval')->nullable();
            $table->json('finance_approval')->nullable();
            $table->json('president_approval')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn([
                'related_mrf_no', 'date_opened', 'hiring_status', 'company_name', 'office_branch_site',
                'department_unit', 'hiring_manager', 'department_superior', 'no_of_vacancies', 'position_level',
                'reports_to', 'min_salary_offer', 'max_salary_offer', 'salary_grade', 'applicable_region',
                'applicable_area', 'current_daily_min_wage', 'monthly_equivalent', 'wage_compliance',
                'benefits_package', 'work_schedule', 'rest_days', 'education_req', 'experience_req',
                'skills_req', 'licenses_req', 'preferred_qualifications', 'duties_responsibilities',
                'recruitment_channels', 'screening_flow', 'date_needed', 'posting_start_date',
                'target_hire_date', 'human_capital_approval', 'hiring_manager_approval', 'finance_approval',
                'president_approval'
            ]);
        });
    }
};
