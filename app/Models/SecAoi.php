<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecAoi extends Model
{
    protected $fillable = [
        'company_id',
        'corporation_name',
        'company_reg_no',
        'principal_address',
        'par_value',
        'authorized_capital_stock',
        'directors',
        'type_of_formation',
        'aoi_version',
        'aoi_type',
        'uploaded_by',
        'date_upload',
        'file_path'
    ];
}
