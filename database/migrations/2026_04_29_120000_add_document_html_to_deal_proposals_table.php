<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deal_proposals', function (Blueprint $table): void {
            if (! Schema::hasColumn('deal_proposals', 'document_html')) {
                $table->longText('document_html')->nullable()->after('prepared_by_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deal_proposals', function (Blueprint $table): void {
            if (Schema::hasColumn('deal_proposals', 'document_html')) {
                $table->dropColumn('document_html');
            }
        });
    }
};
