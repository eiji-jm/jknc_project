<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('cif_access_token')->nullable()->after('cif_no');
            $table->timestamp('cif_access_expires_at')->nullable()->after('cif_access_token');
            $table->string('cif_form_sent_to_email')->nullable()->after('cif_access_expires_at');
            $table->timestamp('cif_form_sent_at')->nullable()->after('cif_form_sent_to_email');
            $table->string('specimen_access_token')->nullable()->after('cif_form_sent_at');
            $table->timestamp('specimen_access_expires_at')->nullable()->after('specimen_access_token');
            $table->string('specimen_form_sent_to_email')->nullable()->after('specimen_access_expires_at');
            $table->timestamp('specimen_form_sent_at')->nullable()->after('specimen_form_sent_to_email');

            $table->unique('cif_access_token');
            $table->unique('specimen_access_token');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropUnique(['cif_access_token']);
            $table->dropUnique(['specimen_access_token']);
            $table->dropColumn([
                'cif_access_token',
                'cif_access_expires_at',
                'cif_form_sent_to_email',
                'cif_form_sent_at',
                'specimen_access_token',
                'specimen_access_expires_at',
                'specimen_form_sent_to_email',
                'specimen_form_sent_at',
            ]);
        });
    }
};
