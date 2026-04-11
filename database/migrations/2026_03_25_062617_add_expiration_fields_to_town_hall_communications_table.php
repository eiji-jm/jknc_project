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
                $table->dateTime('expires_at')->nullable();
            }

            if (!Schema::hasColumn('townhall_communications', 'is_archived')) {
                $table->boolean('is_archived')->default(false);
            }

            if (!Schema::hasColumn('townhall_communications', 'archived_at')) {
                $table->dateTime('archived_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('townhall_communications', 'archived_at')) {
                $columnsToDrop[] = 'archived_at';
            }

            if (Schema::hasColumn('townhall_communications', 'is_archived')) {
                $columnsToDrop[] = 'is_archived';
            }

            if (Schema::hasColumn('townhall_communications', 'expires_at')) {
                $columnsToDrop[] = 'expires_at';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
