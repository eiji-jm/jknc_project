<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {
            if (!Schema::hasColumn('townhall_communications', 'priority')) {
                $table->string('priority')->nullable()->after('to_for');
            }
        });
    }

    public function down(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {
            if (Schema::hasColumn('townhall_communications', 'priority')) {
                $table->dropColumn('priority');
            }
        });
    }
};