<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('secretary_certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('secretary_certificates', 'minute_id')) {
                $table->foreignId('minute_id')->nullable()->after('type_of_meeting')->constrained('minutes')->nullOnDelete();
            }

            if (!Schema::hasColumn('secretary_certificates', 'minutes_ref')) {
                $table->string('minutes_ref')->nullable()->after('meeting_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('secretary_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('secretary_certificates', 'minute_id')) {
                $table->dropConstrainedForeignId('minute_id');
            }

            if (Schema::hasColumn('secretary_certificates', 'minutes_ref')) {
                $table->dropColumn('minutes_ref');
            }
        });
    }
};
