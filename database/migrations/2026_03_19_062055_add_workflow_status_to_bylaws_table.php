<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bylaws', function (Blueprint $table) {
            $table->string('workflow_status')->default('Uploaded')->after('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('bylaws', function (Blueprint $table) {
            $table->dropColumn('workflow_status');
        });
    }
};