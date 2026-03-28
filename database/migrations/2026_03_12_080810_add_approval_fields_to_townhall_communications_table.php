<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('townhall_communications')) {
            return;
        }

        Schema::table('townhall_communications', function (Blueprint $table) {
            if (! Schema::hasColumn('townhall_communications', 'approval_status')) {
                $table->string('approval_status')->default('Pending')->after('status');
            }

            if (! Schema::hasColumn('townhall_communications', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }

            if (! Schema::hasColumn('townhall_communications', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (! Schema::hasColumn('townhall_communications', 'approval_notes')) {
                $table->text('approval_notes')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('townhall_communications')) {
            return;
        }

        Schema::table('townhall_communications', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('townhall_communications', 'approval_status') ? 'approval_status' : null,
                Schema::hasColumn('townhall_communications', 'approved_by') ? 'approved_by' : null,
                Schema::hasColumn('townhall_communications', 'approved_at') ? 'approved_at' : null,
                Schema::hasColumn('townhall_communications', 'approval_notes') ? 'approval_notes' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
