<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_permissions')) {
            return;
        }

        Schema::table('user_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('user_permissions', 'create_corporate')) {
                $table->boolean('create_corporate')->default(false)->after('create_townhall');
            }

            if (!Schema::hasColumn('user_permissions', 'approve_corporate')) {
                $table->boolean('approve_corporate')->default(false)->after('create_corporate');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('user_permissions')) {
            return;
        }

        Schema::table('user_permissions', function (Blueprint $table) {
            if (Schema::hasColumn('user_permissions', 'approve_corporate')) {
                $table->dropColumn('approve_corporate');
            }

            if (Schema::hasColumn('user_permissions', 'create_corporate')) {
                $table->dropColumn('create_corporate');
            }
        });
    }
};
