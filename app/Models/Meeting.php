<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'title', 'owner', 'date', 'time', 'duration', 'location', 'attendees', 'status', 'description', 'has_video', 'has_audio', 'has_transcript', 'has_minutes', 'video_path'
    ];
    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
