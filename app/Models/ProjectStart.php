<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ProjectStart extends Model
{
    protected $fillable = [
        'project_id',
        'start_code',
        'form_date',
        'date_started',
        'date_completed',
        'status',
        'checklist',
        'kyc_requirements',
        'engagement_requirements',
        'approval_steps',
        'routing',
        'clearance',
        'rejection_reason',
        'approved_at',
        'approved_by_name',
        'rejected_at',
        'rejected_by_name',
    ];

    protected $casts = [
        'form_date' => 'date',
        'date_started' => 'date',
        'date_completed' => 'date',
        'checklist' => 'array',
        'kyc_requirements' => 'array',
        'engagement_requirements' => 'array',
        'approval_steps' => 'array',
        'routing' => 'array',
        'clearance' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProjectStart $start): void {
            if (blank($start->start_code)) {
                $start->start_code = static::generateNextCode();
            }
        });
    }

    public static function generateNextCode(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        $prefix = sprintf('START-%d-', $year);

        $nextNumber = DB::transaction(function () use ($prefix): int {
            $latestCode = static::query()
                ->where('start_code', 'like', $prefix.'%')
                ->orderByDesc('start_code')
                ->lockForUpdate()
                ->value('start_code');

            if (! is_string($latestCode) || ! preg_match('/^START-\d{4}-\d{3}$/', $latestCode)) {
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
