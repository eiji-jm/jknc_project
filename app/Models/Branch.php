<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'office_id',
        'branch_name',
        'branch_address',
        'branch_head',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}