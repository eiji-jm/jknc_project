<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bir_taxes', function (Blueprint $table) {
            if (!Schema::hasColumn('bir_taxes', 'notes')) {
                $table->longText('notes')->nullable()->after('approved_document_path');
            }

            if (!Schema::hasColumn('bir_taxes', 'notes_visible_to')) {
                $table->string('notes_visible_to')->nullable()->after('notes');
            }
        });

        Schema::table('nat_govs', function (Blueprint $table) {
            if (!Schema::hasColumn('nat_govs', 'notes')) {
                $table->longText('notes')->nullable()->after('approved_document_path');
            }

            if (!Schema::hasColumn('nat_govs', 'notes_visible_to')) {
                $table->string('notes_visible_to')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bir_taxes', function (Blueprint $table) {
            foreach (['notes_visible_to', 'notes'] as $column) {
                if (Schema::hasColumn('bir_taxes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('nat_govs', function (Blueprint $table) {
            foreach (['notes_visible_to', 'notes'] as $column) {
                if (Schema::hasColumn('nat_govs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
