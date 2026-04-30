<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            if (! Schema::hasColumn('deals', 'assigned_finance_user_id')) {
                $table->foreignId('assigned_finance_user_id')
                    ->nullable()
                    ->after('assigned_associate')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'assigned_finance_user_id')) {
                $table->dropConstrainedForeignId('assigned_finance_user_id');
            }
        });
    }
};
