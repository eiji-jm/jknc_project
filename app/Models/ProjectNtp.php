<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ProjectNtp extends Model
{
    protected $fillable = [
        'project_id',
        'ntp_number',
        'reference_type',
        'reference_number',
        'date_issued',
        'payload',
        'client_access_token',
        'client_access_expires_at',
        'client_form_sent_to_email',
        'client_form_sent_at',
        'client_response_status',
        'client_approved_at',
        'client_approved_name',
        'client_response_notes',
        'client_attachment_path',
    ];

    protected $casts = [
        'date_issued' => 'date',
        'payload' => 'array',
        'client_access_expires_at' => 'datetime',
        'client_form_sent_at' => 'datetime',
        'client_approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProjectNtp $ntp): void {
            if (blank($ntp->ntp_number)) {
                $ntp->ntp_number = static::generateNextCode();
            }
        });
    }

    public static function generateNextCode(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        $prefix = sprintf('NTP-%d-', $year);

        $nextNumber = DB::transaction(function () use ($prefix): int {
            $latestCode = static::query()
                ->where('ntp_number', 'like', $prefix.'%')
                ->orderByDesc('ntp_number')
                ->lockForUpdate()
                ->value('ntp_number');

            if (! is_string($latestCode) || ! preg_match('/^NTP-\d{4}-\d{3}$/', $latestCode)) {
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
