<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bylaw extends Model
{
    protected $fillable = [
        'corporation_name',
        'company_reg_no',
        'type_of_formation',
        'aoi_version',
        'aoi_type',
        'aoi_date',
        'regular_asm',
        'asm_notice',
        'regular_bodm',
        'bodm_notice',
        'uploaded_by',
        'date_upload',
        'file_path',
        'approval_status',
        'submitted_by',
        'approved_by',
        'approved_at',
        'review_note',
    ];
}