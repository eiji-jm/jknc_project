<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gis_records', function (Blueprint $table) {
            $table->string('parent_company_name')->nullable()->after('geo_code');
            $table->string('parent_company_sec_no')->nullable()->after('parent_company_name');
            $table->string('parent_company_address')->nullable()->after('parent_company_sec_no');

            $table->string('subsidiary_name')->nullable()->after('parent_company_address');
            $table->string('subsidiary_sec_no')->nullable()->after('subsidiary_name');
            $table->string('subsidiary_address')->nullable()->after('subsidiary_sec_no');
        });
    }

    public function down(): void
    {
        Schema::table('gis_records', function (Blueprint $table) {
            $table->dropColumn([
                'parent_company_name',
                'parent_company_sec_no',
                'parent_company_address',
                'subsidiary_name',
                'subsidiary_sec_no',
                'subsidiary_address',
            ]);
        });
    }
};