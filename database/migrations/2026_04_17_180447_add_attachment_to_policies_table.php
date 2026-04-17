<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            if (!Schema::hasColumn('policies', 'attachment')) {
                $table->string('attachment')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            if (Schema::hasColumn('policies', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};
