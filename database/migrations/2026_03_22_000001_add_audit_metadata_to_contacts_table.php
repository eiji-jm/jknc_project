<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'business_date')) {
                $table->date('business_date')->nullable()->after('id');
            }

            if (! Schema::hasColumn('contacts', 'created_by')) {
                $table->string('created_by')->nullable()->after('kyc_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'business_date')) {
                $table->dropColumn('business_date');
            }

            if (Schema::hasColumn('contacts', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};
