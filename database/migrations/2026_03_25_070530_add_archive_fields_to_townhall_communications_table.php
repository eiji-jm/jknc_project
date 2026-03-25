<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {

            if (!Schema::hasColumn('townhall_communications', 'expires_at')) {
                $table->dateTime('expires_at')->nullable()->after('created_by');
            }

            if (!Schema::hasColumn('townhall_communications', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('expires_at');
            }

            if (!Schema::hasColumn('townhall_communications', 'archived_at')) {
                $table->dateTime('archived_at')->nullable()->after('is_archived');
            }
        });
    }

    public function down(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {

            if (Schema::hasColumn('townhall_communications', 'archived_at')) {
                $table->dropColumn('archived_at');
            }

            if (Schema::hasColumn('townhall_communications', 'is_archived')) {
                $table->dropColumn('is_archived');
            }

            if (Schema::hasColumn('townhall_communications', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};
