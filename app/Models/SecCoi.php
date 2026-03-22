<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecCoi extends Model
{
    protected $table = 'sec_coi';

    protected $fillable = [
        'company_id',
        'corporate_name',
        'company_reg_no',
        'issued_by',
        'issued_on',
        'date_upload',
        'file_path'
    ];
}
