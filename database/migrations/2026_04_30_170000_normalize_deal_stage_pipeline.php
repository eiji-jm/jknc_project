<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('deal_stages')) {
            return;
        }

        $canonical = [
            ['name' => 'Inquiry', 'order' => 1, 'color' => '#2563eb'],
            ['name' => 'Qualification', 'order' => 2, 'color' => '#4f46e5'],
            ['name' => 'Consultation', 'order' => 3, 'color' => '#0891b2'],
            ['name' => 'Proposal', 'order' => 4, 'color' => '#d97706'],
            ['name' => 'Negotiation', 'order' => 5, 'color' => '#ea580c'],
            ['name' => 'Payment', 'order' => 6, 'color' => '#059669'],
            ['name' => 'Activation', 'order' => 7, 'color' => '#7c3aed'],
            ['name' => 'Closed Won', 'order' => 8, 'color' => '#16a34a'],
            ['name' => 'Closed Lost', 'order' => 9, 'color' => '#dc2626'],
        ];

        foreach ($canonical as $stage) {
            $existing = DB::table('deal_stages')
                ->whereRaw('LOWER(TRIM(name)) = ?', [Str::lower($stage['name'])])
                ->orderBy('id')
                ->first();

            if (! $existing) {
                DB::table('deal_stages')->insert([
                    ...$stage,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                continue;
            }

            DB::table('deal_stages')->where('id', $existing->id)->update([
                'name' => $stage['name'],
                'order' => $stage['order'],
                'color' => $stage['color'],
                'updated_at' => now(),
            ]);

            DB::table('deal_stages')
                ->where('id', '!=', $existing->id)
                ->whereRaw('LOWER(TRIM(name)) = ?', [Str::lower($stage['name'])])
                ->orderBy('id')
                ->get()
                ->each(function ($duplicate) use ($existing, $stage): void {
                    DB::table('deals')->where('stage_id', $duplicate->id)->update([
                        'stage_id' => $existing->id,
                        'stage' => $stage['name'],
                    ]);

                    DB::table('deal_stages')->where('id', $duplicate->id)->delete();
                });

            DB::table('deals')
                ->whereRaw('LOWER(TRIM(stage)) = ?', [Str::lower($stage['name'])])
                ->update([
                    'stage_id' => $existing->id,
                    'stage' => $stage['name'],
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('deal_stages')) {
            return;
        }

        DB::table('deal_stages')
            ->where('name', 'Closed Won')
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('deals')
                    ->whereColumn('deals.stage_id', 'deal_stages.id');
            })
            ->delete();
    }
};
