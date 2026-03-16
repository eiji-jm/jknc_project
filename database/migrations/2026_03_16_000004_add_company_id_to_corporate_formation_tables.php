<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['sec_coi', 'sec_aois', 'bylaws', 'gis_records'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->unsignedBigInteger('company_id')->nullable()->after('id')->index();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['sec_coi', 'sec_aois', 'bylaws', 'gis_records'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('company_id');
                });
            }
        }
    }
};
