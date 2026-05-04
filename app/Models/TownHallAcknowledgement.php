<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TownHallAcknowledgement extends Model
{
    protected $table = 'townhall_acknowledgements';

    protected $fillable = [
        'townhall_communication_id',
        'user_id',
        'acknowledged_at',
    ];
}
