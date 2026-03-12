<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribedCapital extends Model
{
    protected $fillable = [
        'gis_id',
        'nationality',
        'no_of_stockholders',
        'share_type',
        'number_of_shares',
        'par_value',
        'amount',
        'ownership_percentage'
    ];
}