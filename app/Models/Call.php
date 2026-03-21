<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = [
        'contact', 'to', 'from', 'type', 'start_time', 'start_hour', 'duration', 'related_to', 'owner', 'completed', 'purpose', 'agenda'
    ];
    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
