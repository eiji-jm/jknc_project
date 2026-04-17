<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'position', 'email', 'phone', 'cv_path', 'cover_letter', 'status', 'applied_date'
    ];
}
