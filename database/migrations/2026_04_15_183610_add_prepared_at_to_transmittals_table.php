<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transmittals', function (Blueprint $table) {
            if (! Schema::hasColumn('transmittals', 'prepared_at')) {
                $table->dateTime('prepared_at')->nullable()->after('prepared_by_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transmittals', function (Blueprint $table) {
            if (Schema::hasColumn('transmittals', 'prepared_at')) {
                $table->dropColumn('prepared_at');
            }
        });
    }
};