<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('role_permissions')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                if (! Schema::hasColumn('role_permissions', 'access_human_capital')) {
                    $table->boolean('access_human_capital')->default(false)->after('access_sales_marketing');
                }
            });

            DB::table('role_permissions')
                ->whereIn('role', ['SuperAdmin', 'Admin'])
                ->update(['access_human_capital' => 1]);
        }

        if (Schema::hasTable('user_permissions')) {
            Schema::table('user_permissions', function (Blueprint $table) {
                if (! Schema::hasColumn('user_permissions', 'access_human_capital')) {
                    $table->boolean('access_human_capital')->default(false)->after('access_company');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_permissions') && Schema::hasColumn('user_permissions', 'access_human_capital')) {
            Schema::table('user_permissions', function (Blueprint $table) {
                $table->dropColumn('access_human_capital');
            });
        }

        if (Schema::hasTable('role_permissions') && Schema::hasColumn('role_permissions', 'access_human_capital')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->dropColumn('access_human_capital');
            });
        }
    }
};