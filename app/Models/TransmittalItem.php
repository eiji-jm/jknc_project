<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransmittalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transmittal_id',
        'item_no',
        'particular',
        'unique_id',
        'qty',
        'description',
        'remarks',
        'attachment_path',
    ];

    public function transmittal()
    {
        return $this->belongsTo(Transmittal::class);
    }
}