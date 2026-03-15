<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'from', 'to', 'related_to', 'host'
    ];
    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
