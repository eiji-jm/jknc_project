<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('customer_type')->nullable()->after('id');
            $table->string('salutation')->nullable()->after('customer_type');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('company_address')->nullable()->after('company_name');
            $table->string('contact_address')->nullable()->after('company_address');
            $table->string('position')->nullable()->after('contact_address');
            $table->string('service_inquiry_type')->nullable()->after('position');
            $table->string('referred_by')->nullable()->after('lead_source');
            $table->string('lead_stage')->nullable()->after('referred_by');
            $table->text('recommendation')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'customer_type',
                'salutation',
                'middle_name',
                'company_address',
                'contact_address',
                'position',
                'service_inquiry_type',
                'referred_by',
                'lead_stage',
                'recommendation',
            ]);
        });
    }
};
