<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = [
        'contact', 'to', 'from', 'type', 'start_time', 'start_hour', 'duration', 'related_to', 'owner', 'completed', 'purpose', 'agenda', 'audio_path'
    ];

    protected $appends = ['audio_url'];

    public function getAudioUrlAttribute()
    {
        if (!$this->audio_path) return null;
        return str_starts_with($this->audio_path, 'http') ? $this->audio_path : url($this->audio_path);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
