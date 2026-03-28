<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('correspondences', function (Blueprint $table) {
            if (!Schema::hasColumn('correspondences', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->nullable()->after('user');
            }

            if (!Schema::hasColumn('correspondences', 'workflow_status')) {
                $table->string('workflow_status')->default('Uploaded')->after('sent_via');
            }

            if (!Schema::hasColumn('correspondences', 'approval_status')) {
                $table->string('approval_status')->default('Pending')->after('workflow_status');
            }

            if (!Schema::hasColumn('correspondences', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }

            if (!Schema::hasColumn('correspondences', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('correspondences', 'review_note')) {
                $table->text('review_note')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('correspondences', function (Blueprint $table) {
            $columns = [
                'submitted_by',
                'workflow_status',
                'approval_status',
                'approved_by',
                'approved_at',
                'review_note',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('correspondences', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};