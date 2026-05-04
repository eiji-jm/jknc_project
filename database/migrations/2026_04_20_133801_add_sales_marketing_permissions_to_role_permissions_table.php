<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('role_permissions')) {
            return;
        }

        Schema::table('role_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('role_permissions', 'create_sales_marketing')) {
                $table->boolean('create_sales_marketing')->default(false)->after('approve_corporate');
            }

            if (!Schema::hasColumn('role_permissions', 'approve_sales_marketing')) {
                $table->boolean('approve_sales_marketing')->default(false)->after('create_sales_marketing');
            }

            if (!Schema::hasColumn('role_permissions', 'access_sales_marketing')) {
                $table->boolean('access_sales_marketing')->default(false)->after('access_company');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('role_permissions')) {
            return;
        }

        Schema::table('role_permissions', function (Blueprint $table) {
            if (Schema::hasColumn('role_permissions', 'access_sales_marketing')) {
                $table->dropColumn('access_sales_marketing');
            }

            if (Schema::hasColumn('role_permissions', 'approve_sales_marketing')) {
                $table->dropColumn('approve_sales_marketing');
            }

            if (Schema::hasColumn('role_permissions', 'create_sales_marketing')) {
                $table->dropColumn('create_sales_marketing');
            }
        });
    }
};