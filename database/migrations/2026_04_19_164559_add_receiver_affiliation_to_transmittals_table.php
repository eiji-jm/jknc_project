<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transmittals', function (Blueprint $table) {
            if (! Schema::hasColumn('transmittals', 'receiver_affiliation')) {
                $table->string('receiver_affiliation')->nullable()->after('received_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transmittals', function (Blueprint $table) {
            if (Schema::hasColumn('transmittals', 'receiver_affiliation')) {
                $table->dropColumn('receiver_affiliation');
            }
        });
    }
};