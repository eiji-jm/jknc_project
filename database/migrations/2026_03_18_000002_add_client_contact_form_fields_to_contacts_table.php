<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->date('intake_date')->nullable()->after('id');
            $table->string('client_status')->nullable()->after('customer_type');
            $table->string('middle_initial')->nullable()->after('first_name');
            $table->string('name_extension')->nullable()->after('last_name');
            $table->string('sex')->nullable()->after('name_extension');
            $table->date('date_of_birth')->nullable()->after('sex');

            $table->string('business_type_organization')->nullable()->after('position');
            $table->string('organization_type')->nullable()->after('business_type_organization');
            $table->string('organization_type_other')->nullable()->after('organization_type');
            $table->string('nature_of_business')->nullable()->after('organization_type_other');
            $table->decimal('capitalization_amount', 14, 2)->nullable()->after('nature_of_business');
            $table->string('ownership_structure')->nullable()->after('capitalization_amount');
            $table->decimal('previous_year_revenue', 14, 2)->nullable()->after('ownership_structure');
            $table->string('years_operating')->nullable()->after('previous_year_revenue');
            $table->decimal('projected_current_year_revenue', 14, 2)->nullable()->after('years_operating');
            $table->string('ownership_flag')->nullable()->after('projected_current_year_revenue');
            $table->text('foreign_business_nature')->nullable()->after('ownership_flag');

            $table->json('service_inquiry_types')->nullable()->after('service_inquiry_type');
            $table->string('service_inquiry_other')->nullable()->after('service_inquiry_types');
            $table->text('inquiry')->nullable()->after('service_inquiry_other');

            $table->text('jknc_notes')->nullable()->after('inquiry');
            $table->text('sales_marketing')->nullable()->after('jknc_notes');
            $table->string('consultant_lead')->nullable()->after('sales_marketing');
            $table->string('lead_associate')->nullable()->after('consultant_lead');

            $table->json('recommendation_options')->nullable()->after('recommendation');
            $table->string('recommendation_other')->nullable()->after('recommendation_options');

            $table->json('lead_source_channels')->nullable()->after('lead_source');
            $table->string('lead_source_other')->nullable()->after('lead_source_channels');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'intake_date',
                'client_status',
                'middle_initial',
                'name_extension',
                'sex',
                'date_of_birth',
                'business_type_organization',
                'organization_type',
                'organization_type_other',
                'nature_of_business',
                'capitalization_amount',
                'ownership_structure',
                'previous_year_revenue',
                'years_operating',
                'projected_current_year_revenue',
                'ownership_flag',
                'foreign_business_nature',
                'service_inquiry_types',
                'service_inquiry_other',
                'inquiry',
                'jknc_notes',
                'sales_marketing',
                'consultant_lead',
                'lead_associate',
                'recommendation_options',
                'recommendation_other',
                'lead_source_channels',
                'lead_source_other',
            ]);
        });
    }
};
