<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ProjectSowReport extends Model
{
    protected $fillable = [
        'project_id',
        'report_number',
        'version_number',
        'date_prepared',
        'within_scope_items',
        'out_of_scope_items',
        'status_summary',
        'project_completion_percentage',
        'key_issues',
        'recommendations',
        'way_forward',
        'client_confirmation_name',
        'internal_approval',
    ];

    protected $casts = [
        'date_prepared' => 'date',
        'within_scope_items' => 'array',
        'out_of_scope_items' => 'array',
        'status_summary' => 'array',
        'internal_approval' => 'array',
        'project_completion_percentage' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProjectSowReport $report): void {
            if (blank($report->report_number)) {
                $report->report_number = static::generateNextCode();
            }
        });
    }

    public static function generateNextCode(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        $prefix = sprintf('SOWR-%d-', $year);

        $nextNumber = DB::transaction(function () use ($prefix): int {
            $latestCode = static::query()
                ->where('report_number', 'like', $prefix.'%')
                ->orderByDesc('report_number')
                ->lockForUpdate()
                ->value('report_number');

            if (! is_string($latestCode) || ! preg_match('/^SOWR-\d{4}-\d{3}$/', $latestCode)) {
                return 1;
            }

            return ((int) substr($latestCode, -3)) + 1;
        });

        return $prefix.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
