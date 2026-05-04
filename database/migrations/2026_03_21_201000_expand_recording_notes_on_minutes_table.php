<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minutes', function (Blueprint $table) {
            $table->longText('recording_notes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('minutes', function (Blueprint $table) {
            $table->string('recording_notes')->nullable()->change();
        });
    }
};
