<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_starts', function (Blueprint $table): void {
            if (! Schema::hasColumn('project_starts', 'form_date')) {
                $table->date('form_date')->nullable()->after('start_code');
            }

            if (! Schema::hasColumn('project_starts', 'kyc_requirements')) {
                $table->json('kyc_requirements')->nullable()->after('checklist');
            }

            if (! Schema::hasColumn('project_starts', 'approval_steps')) {
                $table->json('approval_steps')->nullable()->after('engagement_requirements');
            }

            if (! Schema::hasColumn('project_starts', 'clearance')) {
                $table->json('clearance')->nullable()->after('approval_steps');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_starts', function (Blueprint $table): void {
            foreach (['clearance', 'approval_steps', 'kyc_requirements', 'form_date'] as $column) {
                if (Schema::hasColumn('project_starts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
