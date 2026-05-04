<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('deals') || ! Schema::hasTable('deal_stages') || ! Schema::hasColumn('deals', 'stage_id')) {
            return;
        }

        $stageMap = DB::table('deal_stages')->pluck('id', 'name');

        DB::table('deals')
            ->select(['id', 'stage', 'stage_id'])
            ->orderBy('id')
            ->get()
            ->each(function ($deal) use ($stageMap): void {
                if (! empty($deal->stage_id)) {
                    return;
                }

                $mappedId = $stageMap[$deal->stage] ?? null;
                if ($mappedId) {
                    DB::table('deals')->where('id', $deal->id)->update(['stage_id' => $mappedId]);
                }
            });

        $missingCount = DB::table('deals')->whereNull('stage_id')->count();
        if ($missingCount > 0) {
            throw new RuntimeException('Some deals still have null stage_id. Fix stage mappings before continuing.');
        }

        Schema::table('deals', function (Blueprint $table) {
            try {
                $table->foreign('stage_id')->references('id')->on('deal_stages')->nullOnDelete();
            } catch (Throwable) {
                // Ignore if the foreign key already exists or the database does not allow re-adding it.
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('deals') || ! Schema::hasColumn('deals', 'stage_id')) {
            return;
        }

        Schema::table('deals', function (Blueprint $table) {
            try {
                $table->dropForeign(['stage_id']);
            } catch (Throwable) {
                // Ignore if the foreign key does not exist.
            }
        });
    }
};
