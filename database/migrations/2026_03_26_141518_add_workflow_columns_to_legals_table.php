<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('legals', function (Blueprint $table) {
            if (!Schema::hasColumn('legals', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->nullable()->after('user');
            }

            if (!Schema::hasColumn('legals', 'workflow_status')) {
                $table->string('workflow_status')->default('Uploaded')->after('user');
            }

            if (!Schema::hasColumn('legals', 'approval_status')) {
                $table->string('approval_status')->default('Pending')->after('workflow_status');
            }

            if (!Schema::hasColumn('legals', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }

            if (!Schema::hasColumn('legals', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('legals', 'review_note')) {
                $table->text('review_note')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('legals', function (Blueprint $table) {
            foreach ([
                'submitted_by',
                'workflow_status',
                'approval_status',
                'approved_by',
                'approved_at',
                'review_note',
            ] as $column) {
                if (Schema::hasColumn('legals', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};