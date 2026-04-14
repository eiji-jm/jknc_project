<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('role_permissions')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                if (! Schema::hasColumn('role_permissions', 'create_townhall')) {
                    $table->boolean('create_townhall')->default(false)->after('approve_townhall');
                }

                if (! Schema::hasColumn('role_permissions', 'create_corporate')) {
                    $table->boolean('create_corporate')->default(false)->after('create_townhall');
                }

                if (! Schema::hasColumn('role_permissions', 'approve_corporate')) {
                    $table->boolean('approve_corporate')->default(false)->after('create_corporate');
                }
            });

            return;
        }

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();

            $table->string('role')->unique();

            $table->boolean('manage_users')->default(false);
            $table->boolean('access_admin_dashboard')->default(false);

            $table->boolean('create_townhall')->default(false);
            $table->boolean('approve_townhall')->default(false);

            $table->boolean('create_corporate')->default(false);
            $table->boolean('approve_corporate')->default(false);

            $table->boolean('access_townhall')->default(false);
            $table->boolean('access_corporate')->default(false);
            $table->boolean('access_activities')->default(false);
            $table->boolean('access_contacts')->default(false);
            $table->boolean('access_company')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('role_permissions')) {
            return;
        }

        if (
            Schema::hasColumn('role_permissions', 'approve_corporate')
            || Schema::hasColumn('role_permissions', 'create_corporate')
            || Schema::hasColumn('role_permissions', 'create_townhall')
        ) {
            Schema::table('role_permissions', function (Blueprint $table) {
                if (Schema::hasColumn('role_permissions', 'approve_corporate')) {
                    $table->dropColumn('approve_corporate');
                }

                if (Schema::hasColumn('role_permissions', 'create_corporate')) {
                    $table->dropColumn('create_corporate');
                }

                if (Schema::hasColumn('role_permissions', 'create_townhall')) {
                    $table->dropColumn('create_townhall');
                }
            });
        }
    }
};
