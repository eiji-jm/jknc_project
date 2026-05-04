<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_edit_user_roles')->default(false)->after('role');
            $table->boolean('can_delete_users')->default(false)->after('can_edit_user_roles');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_edit_user_roles',
                'can_delete_users',
            ]);
        });
    }
};
