<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {
            $table->string('source_type')->nullable()->after('created_by');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            $table->date('deadline_date')->nullable()->after('source_id');
        });
    }

    public function down(): void
    {
        Schema::table('townhall_communications', function (Blueprint $table) {
            $table->dropColumn([
                'source_type',
                'source_id',
                'deadline_date',
            ]);
        });
    }
};
