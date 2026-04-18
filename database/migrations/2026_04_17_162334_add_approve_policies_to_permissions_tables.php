<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('role_permissions') && !Schema::hasColumn('role_permissions', 'approve_policies')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->boolean('approve_policies')->default(false)->after('approve_corporate');
            });
        }

        if (Schema::hasTable('user_permissions') && !Schema::hasColumn('user_permissions', 'approve_policies')) {
            Schema::table('user_permissions', function (Blueprint $table) {
                $table->boolean('approve_policies')->default(false)->after('approve_corporate');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('role_permissions') && Schema::hasColumn('role_permissions', 'approve_policies')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->dropColumn('approve_policies');
            });
        }

        if (Schema::hasTable('user_permissions') && Schema::hasColumn('user_permissions', 'approve_policies')) {
            Schema::table('user_permissions', function (Blueprint $table) {
                $table->dropColumn('approve_policies');
            });
        }
    }
};
