<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bir_taxes', function (Blueprint $table) {
            $table->string('approved_document_path')->nullable()->after('document_path');
        });

        Schema::table('nat_govs', function (Blueprint $table) {
            $table->string('approved_document_path')->nullable()->after('document_path');
        });
    }

    public function down(): void
    {
        Schema::table('bir_taxes', function (Blueprint $table) {
            $table->dropColumn('approved_document_path');
        });

        Schema::table('nat_govs', function (Blueprint $table) {
            $table->dropColumn('approved_document_path');
        });
    }
};
