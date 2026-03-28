<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        DB::table('users')
            ->whereNull('role')
            ->orWhereRaw('TRIM(role) = ?', [''])
            ->update(['role' => 'Employee']);

        DB::table('users')
            ->whereRaw('LOWER(TRIM(role)) IN (?, ?, ?, ?, ?, ?)', [
                'super admin',
                'superadmin',
                'super-admin',
                'super_admin',
                'super administrator',
                'superadministrator',
            ])
            ->update(['role' => 'SuperAdmin']);

        DB::table('users')
            ->whereRaw('LOWER(TRIM(role)) IN (?, ?, ?)', [
                'admin',
                'administrator',
                'admins',
            ])
            ->update(['role' => 'Admin']);

        DB::table('users')
            ->whereRaw('LOWER(TRIM(role)) IN (?, ?, ?, ?, ?, ?)', [
                'employee',
                'employees',
                'staff',
                'user',
                'users',
                'member',
            ])
            ->update(['role' => 'Employee']);

        DB::table('users')
            ->whereNotIn('role', ['SuperAdmin', 'Admin', 'Employee'])
            ->update(['role' => 'Employee']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['SuperAdmin', 'Admin', 'Employee'])->default('Employee')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Employee'])->default('Employee')->change();
        });

        DB::table('users')
            ->where('role', 'SuperAdmin')
            ->update(['role' => 'Admin']);
    }
};
