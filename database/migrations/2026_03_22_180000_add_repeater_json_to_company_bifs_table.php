<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_bifs', function (Blueprint $table) {
            if (! Schema::hasColumn('company_bifs', 'authorized_signatories')) {
                $table->json('authorized_signatories')->nullable()->after('authorized_signatory_position');
            }

            if (! Schema::hasColumn('company_bifs', 'ubos')) {
                $table->json('ubos')->nullable()->after('ubo_position');
            }
        });
    }

    public function down(): void
    {
        Schema::table('company_bifs', function (Blueprint $table) {
            if (Schema::hasColumn('company_bifs', 'authorized_signatories')) {
                $table->dropColumn('authorized_signatories');
            }

            if (Schema::hasColumn('company_bifs', 'ubos')) {
                $table->dropColumn('ubos');
            }
        });
    }
};
