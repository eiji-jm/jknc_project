<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {
            $expiresAfter = Schema::hasColumn('townhall_communications', 'ack_deadline_at')
                ? 'ack_deadline_at'
                : null;

            if (!Schema::hasColumn('townhall_communications', 'expires_at')) {
                $expiresColumn = $table->dateTime('expires_at')->nullable();

                if ($expiresAfter) {
                    $expiresColumn->after($expiresAfter);
                }
            }

            if (!Schema::hasColumn('townhall_communications', 'is_archived')) {
                $archivedColumn = $table->boolean('is_archived')->default(false);

                if (Schema::hasColumn('townhall_communications', 'expires_at')) {
                    $archivedColumn->after('expires_at');
                }
            }

            if (!Schema::hasColumn('townhall_communications', 'archived_at')) {
                $archivedAtColumn = $table->dateTime('archived_at')->nullable();

                if (Schema::hasColumn('townhall_communications', 'is_archived')) {
                    $archivedAtColumn->after('is_archived');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {
            if (Schema::hasColumn('townhall_communications', 'archived_at')) {
                $table->dropColumn('archived_at');
            }

            if (Schema::hasColumn('townhall_communications', 'is_archived')) {
                $table->dropColumn('is_archived');
            }

            if (Schema::hasColumn('townhall_communications', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};
