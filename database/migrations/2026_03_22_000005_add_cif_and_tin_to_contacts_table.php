<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'cif_no')) {
                $table->string('cif_no')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('contacts', 'tin')) {
                $table->string('tin')->nullable()->after('cif_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'tin')) {
                $table->dropColumn('tin');
            }
            if (Schema::hasColumn('contacts', 'cif_no')) {
                $table->dropColumn('cif_no');
            }
        });
    }
};
