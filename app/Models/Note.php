<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['content', 'owner', 'noteable_id', 'noteable_type'];

    public function noteable()
    {
        return $this->morphTo();
    }
}
