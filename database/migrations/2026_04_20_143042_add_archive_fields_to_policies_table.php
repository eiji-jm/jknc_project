<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            if (!Schema::hasColumn('policies', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('workflow_status');
            }

            if (!Schema::hasColumn('policies', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('is_archived');
            }
        });
    }

    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            if (Schema::hasColumn('policies', 'archived_at')) {
                $table->dropColumn('archived_at');
            }

            if (Schema::hasColumn('policies', 'is_archived')) {
                $table->dropColumn('is_archived');
            }
        });
    }
};
