<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            if (! Schema::hasColumn('deals', 'qualification_result')) {
                $table->string('qualification_result')->default('qualified')->after('stage');
            }

            if (! Schema::hasColumn('deals', 'qualification_notes')) {
                $table->text('qualification_notes')->nullable()->after('qualification_result');
            }

            if (! Schema::hasColumn('deals', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('deal_status');
            }

            if (! Schema::hasColumn('deals', 'approved_by_name')) {
                $table->string('approved_by_name')->nullable()->after('approved_at');
            }

            if (! Schema::hasColumn('deals', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_by_name');
            }

            if (! Schema::hasColumn('deals', 'rejected_by_name')) {
                $table->string('rejected_by_name')->nullable()->after('rejected_at');
            }

            if (! Schema::hasColumn('deals', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_by_name');
            }
        });

        if (Schema::hasColumn('deals', 'deal_status')) {
            DB::table('deals')
                ->whereNull('deal_status')
                ->update(['deal_status' => 'pending']);

            DB::table('deals')
                ->whereIn('deal_status', ['approved', 'Approved'])
                ->update(['deal_status' => 'approved']);

            DB::table('deals')
                ->whereIn('deal_status', ['pending', 'Pending'])
                ->update(['deal_status' => 'pending']);

            DB::table('deals')
                ->whereIn('deal_status', ['rejected', 'Rejected'])
                ->update(['deal_status' => 'rejected']);
        }
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            foreach ([
                'qualification_result',
                'qualification_notes',
                'approved_at',
                'approved_by_name',
                'rejected_at',
                'rejected_by_name',
                'rejection_reason',
            ] as $column) {
                if (Schema::hasColumn('deals', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
