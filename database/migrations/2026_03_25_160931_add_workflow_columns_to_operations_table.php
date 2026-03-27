<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            if (!Schema::hasColumn('operations', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->nullable()->after('user');
            }

            if (!Schema::hasColumn('operations', 'workflow_status')) {
                $table->string('workflow_status')->default('Uploaded')->after('status');
            }

            if (!Schema::hasColumn('operations', 'approval_status')) {
                $table->string('approval_status')->default('Pending')->after('workflow_status');
            }

            if (!Schema::hasColumn('operations', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }

            if (!Schema::hasColumn('operations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('operations', 'review_note')) {
                $table->text('review_note')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $columns = [
                'submitted_by',
                'workflow_status',
                'approval_status',
                'approved_by',
                'approved_at',
                'review_note',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('operations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};