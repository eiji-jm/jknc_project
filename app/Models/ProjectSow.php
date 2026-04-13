<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ProjectSow extends Model
{
    protected $fillable = [
        'project_id',
        'sow_number',
        'version_number',
        'date_prepared',
        'within_scope_items',
        'out_of_scope_items',
        'client_confirmation_name',
        'internal_approval',
        'approval_status',
        'approved_at',
        'approved_by_name',
        'ntp_status',
        'client_signed_attachment_path',
        'metadata',
    ];

    protected $casts = [
        'date_prepared' => 'date',
        'within_scope_items' => 'array',
        'out_of_scope_items' => 'array',
        'internal_approval' => 'array',
        'metadata' => 'array',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProjectSow $sow): void {
            if (blank($sow->sow_number)) {
                $sow->sow_number = static::generateNextCode();
            }
        });
    }

    public static function generateNextCode(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        $prefix = sprintf('SOW-%d-', $year);

        $nextNumber = DB::transaction(function () use ($prefix): int {
            $latestCode = static::query()
                ->where('sow_number', 'like', $prefix.'%')
                ->orderByDesc('sow_number')
                ->lockForUpdate()
                ->value('sow_number');

            if (! is_string($latestCode) || ! preg_match('/^SOW-\d{4}-\d{3}$/', $latestCode)) {
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
