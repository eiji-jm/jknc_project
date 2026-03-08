<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GisRecord extends Model
{
    protected $fillable = [

        'uploaded_by',
        'submission_status',
        'receive_on',
        'period_date',

        'company_reg_no',
        'corporation_name',

        'annual_meeting',
        'meeting_type',

        'file'
    ];
}