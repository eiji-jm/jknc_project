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
            if (!Schema::hasColumn('role_permissions', 'access_transmittal')) {
                $table->boolean('access_transmittal')->default(false)->after('access_company');
            }

            if (!Schema::hasColumn('role_permissions', 'access_deals')) {
                $table->boolean('access_deals')->default(false)->after('access_transmittal');
            }

            if (!Schema::hasColumn('role_permissions', 'access_services')) {
                $table->boolean('access_services')->default(false)->after('access_deals');
            }

            if (!Schema::hasColumn('role_permissions', 'access_project')) {
                $table->boolean('access_project')->default(false)->after('access_services');
            }

            if (!Schema::hasColumn('role_permissions', 'access_regular')) {
                $table->boolean('access_regular')->default(false)->after('access_project');
            }

            if (!Schema::hasColumn('role_permissions', 'access_product')) {
                $table->boolean('access_product')->default(false)->after('access_regular');
            }

            if (!Schema::hasColumn('role_permissions', 'access_policies')) {
                $table->boolean('access_policies')->default(false)->after('access_product');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('role_permissions')) {
            return;
        }

        Schema::table('role_permissions', function (Blueprint $table) {
            foreach ([
                'access_policies',
                'access_product',
                'access_regular',
                'access_project',
                'access_services',
                'access_deals',
                'access_transmittal',
            ] as $column) {
                if (Schema::hasColumn('role_permissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};