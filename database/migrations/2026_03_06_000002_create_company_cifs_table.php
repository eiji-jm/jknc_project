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
        Schema::create('company_cifs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('title')->nullable()->default('Client Intake Form');

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('preferred_name')->nullable();
            $table->string('patient_identifier')->nullable();
            $table->string('gender')->nullable();
            $table->string('preferred_pronouns')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('marital_status')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('email')->nullable();
            $table->string('preferred_phone')->nullable();

            $table->string('emergency_contact_1_name')->nullable();
            $table->string('emergency_contact_1_relationship')->nullable();
            $table->string('emergency_contact_1_home_phone')->nullable();
            $table->string('emergency_contact_1_cell_phone')->nullable();
            $table->string('emergency_contact_1_work_phone')->nullable();

            $table->string('emergency_contact_2_name')->nullable();
            $table->string('emergency_contact_2_relationship')->nullable();
            $table->string('emergency_contact_2_home_phone')->nullable();
            $table->string('emergency_contact_2_cell_phone')->nullable();
            $table->string('emergency_contact_2_work_phone')->nullable();

            $table->string('insurance_carrier')->nullable();
            $table->string('insurance_plan')->nullable();
            $table->string('insurance_contact_number')->nullable();
            $table->string('policy_number')->nullable();
            $table->string('group_number')->nullable();
            $table->string('social_security_number')->nullable();

            $table->boolean('under_medical_care')->default(false);
            $table->text('medical_care_for')->nullable();
            $table->string('primary_care_physician')->nullable();
            $table->text('physician_address')->nullable();
            $table->string('physician_contact_number')->nullable();

            $table->text('main_concerns')->nullable();
            $table->text('illness_begin')->nullable();
            $table->text('visit_goals')->nullable();

            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_cifs');
    }
};
