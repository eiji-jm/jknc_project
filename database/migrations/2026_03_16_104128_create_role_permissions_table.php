<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
        Schema::dropIfExists('role_permissions');
    }
};