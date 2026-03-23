<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nat_govs', function (Blueprint $table) {
            $table->date('deadline_date')->nullable()->after('registration_date');
        });
    }

    public function down(): void
    {
        Schema::table('nat_govs', function (Blueprint $table) {
            $table->dropColumn('deadline_date');
        });
    }
};
