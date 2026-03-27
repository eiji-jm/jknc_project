<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gis_records', function (Blueprint $table) {

            $table->date('date_registered')->nullable()->after('corporation_name');
            $table->string('trade_name')->nullable();
            $table->string('fiscal_year_end')->nullable();
            $table->string('tin')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('principal_address')->nullable();
            $table->string('business_address')->nullable();
            $table->string('official_mobile')->nullable();
            $table->string('alternate_mobile')->nullable();
            $table->string('auditor')->nullable();
            $table->string('industry')->nullable();
            $table->string('geo_code')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('gis_records', function (Blueprint $table) {

            $table->dropColumn([
                'date_registered',
                'trade_name',
                'fiscal_year_end',
                'tin',
                'website',
                'email',
                'principal_address',
                'business_address',
                'official_mobile',
                'alternate_mobile',
                'auditor',
                'industry',
                'geo_code'
            ]);

        });
    }
};