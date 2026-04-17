<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'position', 'test_type', 'assessment_date', 'notes', 'score', 'status'
    ];
}
