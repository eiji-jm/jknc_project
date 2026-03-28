<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'cif_submitted_at')) {
                $table->timestamp('cif_submitted_at')->nullable()->after('cif_status');
            }

            if (! Schema::hasColumn('contacts', 'cif_reviewed_at')) {
                $table->timestamp('cif_reviewed_at')->nullable()->after('cif_submitted_at');
            }

            if (! Schema::hasColumn('contacts', 'cif_reviewed_by')) {
                $table->string('cif_reviewed_by')->nullable()->after('cif_reviewed_at');
            }

            if (! Schema::hasColumn('contacts', 'cif_rejection_reason')) {
                $table->text('cif_rejection_reason')->nullable()->after('cif_reviewed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'cif_rejection_reason')) {
                $table->dropColumn('cif_rejection_reason');
            }

            if (Schema::hasColumn('contacts', 'cif_reviewed_by')) {
                $table->dropColumn('cif_reviewed_by');
            }

            if (Schema::hasColumn('contacts', 'cif_reviewed_at')) {
                $table->dropColumn('cif_reviewed_at');
            }

            if (Schema::hasColumn('contacts', 'cif_submitted_at')) {
                $table->dropColumn('cif_submitted_at');
            }
        });
    }
};
