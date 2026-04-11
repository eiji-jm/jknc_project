<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {
            if (!Schema::hasColumn('townhall_communications', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('expires_at');
            }

            if (!Schema::hasColumn('townhall_communications', 'archived_at')) {
                $table->dateTime('archived_at')->nullable()->after('is_archived');
            }

            if (!Schema::hasColumn('townhall_communications', 'ack_deadline_at')) {
                $table->timestamp('ack_deadline_at')->nullable()->after('deadline_date');
            }

            if (!Schema::hasColumn('townhall_communications', 'priority')) {
                $table->string('priority')->nullable()->after('to_for');
            }
        });
    }

    public function down(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {
            $columnsToDrop = [];

            foreach (['archived_at', 'is_archived', 'ack_deadline_at', 'priority'] as $column) {
                if (Schema::hasColumn('townhall_communications', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
