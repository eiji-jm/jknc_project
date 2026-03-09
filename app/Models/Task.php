<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name', 'due_date', 'status', 'priority', 'related_to', 'owner', 'description'
    ];

    //
}
