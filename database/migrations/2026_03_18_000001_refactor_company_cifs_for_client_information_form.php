<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_cifs', function (Blueprint $table) {
            $table->string('cif_no')->nullable()->after('title');
            $table->date('cif_date')->nullable()->after('cif_no');
            $table->string('client_type')->nullable()->after('cif_date');
            $table->string('middle_name')->nullable()->after('last_name');
            $table->string('name_extension')->nullable()->after('middle_name');
            $table->boolean('no_middle_name')->default(false)->after('name_extension');
            $table->boolean('first_name_only')->default(false)->after('no_middle_name');
            $table->string('phone_no')->nullable()->after('email');
            $table->string('mobile_no')->nullable()->after('phone_no');
            $table->string('place_of_birth')->nullable()->after('date_of_birth');
            $table->string('citizenship_status')->nullable()->after('place_of_birth');
            $table->string('nationality')->nullable()->after('citizenship_status');
            $table->string('spouse_name')->nullable()->after('marital_status');
            $table->string('nature_of_work_business')->nullable()->after('spouse_name');
            $table->string('tin')->nullable()->after('nature_of_work_business');
            $table->string('other_government_id')->nullable()->after('tin');
            $table->string('id_number')->nullable()->after('other_government_id');
            $table->string('mothers_maiden_name')->nullable()->after('id_number');
            $table->boolean('source_of_funds_salary')->default(false)->after('mothers_maiden_name');
            $table->boolean('source_of_funds_remittance')->default(false)->after('source_of_funds_salary');
            $table->boolean('source_of_funds_business')->default(false)->after('source_of_funds_remittance');
            $table->boolean('source_of_funds_others')->default(false)->after('source_of_funds_business');
            $table->string('source_of_funds_other_text')->nullable()->after('source_of_funds_others');
            $table->boolean('source_of_funds_commission_fees')->default(false)->after('source_of_funds_other_text');
            $table->boolean('source_of_funds_retirement_pension')->default(false)->after('source_of_funds_commission_fees');
            $table->string('passport_no')->nullable()->after('source_of_funds_retirement_pension');
            $table->date('passport_expiry_date')->nullable()->after('passport_no');
            $table->string('passport_place_of_issue')->nullable()->after('passport_expiry_date');
            $table->string('acr_id_no')->nullable()->after('passport_place_of_issue');
            $table->date('acr_expiry_date')->nullable()->after('acr_id_no');
            $table->string('acr_place_of_issue')->nullable()->after('acr_expiry_date');
            $table->string('visa_status')->nullable()->after('acr_place_of_issue');
            $table->string('signature_printed_name')->nullable()->after('visa_status');
            $table->string('signature_position')->nullable()->after('signature_printed_name');
            $table->string('review_signature_printed_name')->nullable()->after('signature_position');
            $table->string('review_signature_position')->nullable()->after('review_signature_printed_name');
            $table->string('referred_by')->nullable()->after('review_signature_position');
            $table->date('referred_by_date')->nullable()->after('referred_by');
            $table->string('sales_marketing_name')->nullable()->after('referred_by_date');
            $table->string('finance_name')->nullable()->after('sales_marketing_name');
            $table->string('president_name')->nullable()->after('finance_name');
            $table->boolean('onboarding_two_valid_government_ids')->default(false)->after('president_name');
            $table->boolean('onboarding_tin_id')->default(false)->after('onboarding_two_valid_government_ids');
            $table->boolean('onboarding_specimen_signature')->default(false)->after('onboarding_tin_id');
            $table->text('remarks')->nullable()->after('onboarding_specimen_signature');
            $table->timestamp('approved_at')->nullable()->after('submitted_at');
            $table->string('approved_by_name')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_by_name');
            $table->string('rejected_by_name')->nullable()->after('rejected_at');
            $table->text('rejection_reason')->nullable()->after('rejected_by_name');

            $table->index('cif_no');
            $table->index('client_type');
        });
    }

    public function down(): void
    {
        Schema::table('company_cifs', function (Blueprint $table) {
            $table->dropIndex(['cif_no']);
            $table->dropIndex(['client_type']);

            $table->dropColumn([
                'cif_no',
                'cif_date',
                'client_type',
                'middle_name',
                'name_extension',
                'no_middle_name',
                'first_name_only',
                'phone_no',
                'mobile_no',
                'place_of_birth',
                'citizenship_status',
                'nationality',
                'spouse_name',
                'nature_of_work_business',
                'tin',
                'other_government_id',
                'id_number',
                'mothers_maiden_name',
                'source_of_funds_salary',
                'source_of_funds_remittance',
                'source_of_funds_business',
                'source_of_funds_others',
                'source_of_funds_other_text',
                'source_of_funds_commission_fees',
                'source_of_funds_retirement_pension',
                'passport_no',
                'passport_expiry_date',
                'passport_place_of_issue',
                'acr_id_no',
                'acr_expiry_date',
                'acr_place_of_issue',
                'visa_status',
                'signature_printed_name',
                'signature_position',
                'review_signature_printed_name',
                'review_signature_position',
                'referred_by',
                'referred_by_date',
                'sales_marketing_name',
                'finance_name',
                'president_name',
                'onboarding_two_valid_government_ids',
                'onboarding_tin_id',
                'onboarding_specimen_signature',
                'remarks',
                'approved_at',
                'approved_by_name',
                'rejected_at',
                'rejected_by_name',
                'rejection_reason',
            ]);
        });
    }
};
