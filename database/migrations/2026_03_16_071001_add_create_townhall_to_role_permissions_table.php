<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('role_permissions') && !Schema::hasColumn('role_permissions', 'create_townhall')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->boolean('create_townhall')->default(false)->after('approve_townhall');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('role_permissions') && Schema::hasColumn('role_permissions', 'create_townhall')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->dropColumn('create_townhall');
            });
        }
    }
};