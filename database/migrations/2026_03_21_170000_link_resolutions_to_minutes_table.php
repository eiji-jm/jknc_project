<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resolutions', function (Blueprint $table) {
            $table->foreignId('minute_id')->nullable()->after('type_of_meeting')->constrained('minutes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('resolutions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('minute_id');
        });
    }
};
