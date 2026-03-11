<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TownHallCommunication extends Model
{
    protected $table = 'townhall_communications';

    protected $fillable = [
        'ref_no',
        'communication_date',
        'from_name',
        'department_stakeholder',
        'to_for',
        'status',
        'subject',
        'message',
        'cc',
        'additional',
        'attachment',
        'created_by',
    ];
}
