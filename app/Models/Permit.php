<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Permit extends Model
{
    protected $fillable = [
        'permit_type',
        'document_type',
        'permit_number',
        'date_of_registration',
        'approved_date_of_registration',
        'expiration_date_of_registration',
        'user',
        'tin',
        'document_name',
        'document_path',
        'approval_status',
        'workflow_status',
        'submitted_by',
        'approved_by',
        'approved_at',
        'review_note',
    ];

    protected $appends = ['status'];

    protected $casts = [
        'date_of_registration' => 'date:Y-m-d',
        'approved_date_of_registration' => 'date:Y-m-d',
        'expiration_date_of_registration' => 'date:Y-m-d',
        'approved_at' => 'datetime',
    ];

    public function getStatusAttribute()
    {
        if (!$this->expiration_date_of_registration) {
            return 'Active';
        }

        return $this->expiration_date_of_registration->lt(Carbon::today())
            ? 'Expired'
            : 'Active';
    }
}