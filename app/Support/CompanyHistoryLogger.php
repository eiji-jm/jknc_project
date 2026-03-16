<?php

namespace App\Support;

use App\Models\CompanyHistoryEntry;
use Illuminate\Support\Facades\Schema;

class CompanyHistoryLogger
{
    public static function log(int $companyId, array $payload): void
    {
        if (! Schema::hasTable('company_history_entries')) {
            return;
        }

        CompanyHistoryEntry::query()->create([
            'company_id' => $companyId,
            'type' => $payload['type'],
            'title' => $payload['title'],
            'description' => $payload['description'],
            'extra_label' => $payload['extra_label'] ?? null,
            'extra_value' => $payload['extra_value'] ?? null,
            'user_name' => $payload['user_name'],
            'user_initials' => $payload['user_initials'],
            'occurred_at' => $payload['occurred_at'] ?? now(),
        ]);
    }
}
