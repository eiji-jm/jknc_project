<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_permissions')) {
            return;
        }

        Schema::table('user_permissions', function (Blueprint $table) {
            if (! Schema::hasColumn('user_permissions', 'create_corporate')) {
                $table->boolean('create_corporate')->default(false)->after('create_townhall');
            }

            if (! Schema::hasColumn('user_permissions', 'approve_corporate')) {
                $table->boolean('approve_corporate')->default(false)->after('create_corporate');
            }

            if (! Schema::hasColumn('user_permissions', 'approve_policies')) {
                $table->boolean('approve_policies')->default(false)->after('approve_corporate');
            }

            if (! Schema::hasColumn('user_permissions', 'access_transmittal')) {
                $table->boolean('access_transmittal')->default(false)->after('access_company');
            }

            if (! Schema::hasColumn('user_permissions', 'access_deals')) {
                $table->boolean('access_deals')->default(false)->after('access_transmittal');
            }

            if (! Schema::hasColumn('user_permissions', 'access_services')) {
                $table->boolean('access_services')->default(false)->after('access_deals');
            }

            if (! Schema::hasColumn('user_permissions', 'access_project')) {
                $table->boolean('access_project')->default(false)->after('access_services');
            }

            if (! Schema::hasColumn('user_permissions', 'access_regular')) {
                $table->boolean('access_regular')->default(false)->after('access_project');
            }

            if (! Schema::hasColumn('user_permissions', 'access_product')) {
                $table->boolean('access_product')->default(false)->after('access_regular');
            }

            if (! Schema::hasColumn('user_permissions', 'access_policies')) {
                $table->boolean('access_policies')->default(false)->after('access_product');
            }

            if (! Schema::hasColumn('user_permissions', 'create_sales_marketing')) {
                $table->boolean('create_sales_marketing')->default(false)->after('access_policies');
            }

            if (! Schema::hasColumn('user_permissions', 'approve_sales_marketing')) {
                $table->boolean('approve_sales_marketing')->default(false)->after('create_sales_marketing');
            }

            if (! Schema::hasColumn('user_permissions', 'access_sales_marketing')) {
                $table->boolean('access_sales_marketing')->default(false)->after('approve_sales_marketing');
            }

            if (! Schema::hasColumn('user_permissions', 'access_human_capital')) {
                $table->boolean('access_human_capital')->default(false)->after('access_sales_marketing');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('user_permissions')) {
            return;
        }

        Schema::table('user_permissions', function (Blueprint $table) {
            $columns = [
                'access_human_capital',
                'access_sales_marketing',
                'approve_sales_marketing',
                'create_sales_marketing',
                'access_policies',
                'access_product',
                'access_regular',
                'access_project',
                'access_services',
                'access_deals',
                'access_transmittal',
                'approve_policies',
                'approve_corporate',
                'create_corporate',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('user_permissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};