<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('townhall_communications', function (Blueprint $table) {
        $table->string('recipient_label')->default('To')->after('department_stakeholder');
    });
}

public function down(): void
{
    Schema::table('townhall_communications', function (Blueprint $table) {
        $table->dropColumn('recipient_label');
    });
}
};
