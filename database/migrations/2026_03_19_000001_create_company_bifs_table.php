<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_bifs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('title')->nullable();
            $table->string('bif_no')->nullable()->index();
            $table->date('bif_date')->nullable();
            $table->string('client_type')->nullable()->index();
            $table->string('business_organization')->nullable();
            $table->string('business_organization_other')->nullable();
            $table->string('nationality_status')->nullable();
            $table->string('office_type')->nullable();
            $table->string('office_type_other')->nullable();
            $table->string('business_name')->nullable();
            $table->string('alternative_business_name')->nullable();
            $table->text('business_address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('place_of_incorporation')->nullable();
            $table->date('date_of_incorporation')->nullable();
            $table->boolean('industry_services')->default(false);
            $table->boolean('industry_export_import')->default(false);
            $table->boolean('industry_education')->default(false);
            $table->boolean('industry_financial_services')->default(false);
            $table->boolean('industry_transportation')->default(false);
            $table->boolean('industry_distribution')->default(false);
            $table->boolean('industry_manufacturing')->default(false);
            $table->boolean('industry_government')->default(false);
            $table->boolean('industry_wholesale_retail_trade')->default(false);
            $table->boolean('industry_other')->default(false);
            $table->string('industry_other_text')->nullable();
            $table->boolean('capital_micro')->default(false);
            $table->boolean('capital_small')->default(false);
            $table->boolean('capital_medium')->default(false);
            $table->boolean('capital_large')->default(false);
            $table->unsignedInteger('employee_male')->nullable();
            $table->unsignedInteger('employee_female')->nullable();
            $table->unsignedInteger('employee_pwd')->nullable();
            $table->unsignedInteger('employee_total')->nullable();
            $table->boolean('source_revenue_income')->default(false);
            $table->boolean('source_investments')->default(false);
            $table->boolean('source_remittance')->default(false);
            $table->boolean('source_other')->default(false);
            $table->string('source_other_text')->nullable();
            $table->boolean('source_fees')->default(false);
            $table->string('president_name')->nullable();
            $table->string('treasurer_name')->nullable();
            $table->string('authorized_signatory_name')->nullable();
            $table->text('authorized_signatory_address')->nullable();
            $table->string('authorized_signatory_nationality')->nullable();
            $table->date('authorized_signatory_date_of_birth')->nullable();
            $table->string('authorized_signatory_tin')->nullable();
            $table->string('authorized_signatory_position')->nullable();
            $table->string('ubo_name')->nullable();
            $table->text('ubo_address')->nullable();
            $table->string('ubo_nationality')->nullable();
            $table->date('ubo_date_of_birth')->nullable();
            $table->string('ubo_tin')->nullable();
            $table->string('ubo_position')->nullable();
            $table->string('authorized_contact_person_name')->nullable();
            $table->string('authorized_contact_person_position')->nullable();
            $table->string('authorized_contact_person_email')->nullable();
            $table->string('authorized_contact_person_phone')->nullable();
            $table->string('signature_printed_name')->nullable();
            $table->string('signature_position')->nullable();
            $table->string('review_signature_printed_name')->nullable();
            $table->string('review_signature_position')->nullable();
            $table->string('sales_marketing_name')->nullable();
            $table->string('sales_marketing_date_signature')->nullable();
            $table->string('finance_name')->nullable();
            $table->string('finance_date_signature')->nullable();
            $table->string('referred_by')->nullable();
            $table->string('consultant_lead')->nullable();
            $table->string('lead_associate')->nullable();
            $table->string('president_use_only_name')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_by_name')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_bifs');
    }
};
