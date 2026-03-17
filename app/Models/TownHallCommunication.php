<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TownHallCommunication extends Model
{
    protected $table = 'townhall_communications';

    protected $fillable = [
        'ref_no',
        'communication_date',
        'from_name',
        'department_stakeholder',
        'to_for',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'subject',
        'message',
        'cc',
        'additional',
        'attachment',
        'created_by',
    ];

    public function acknowledgements()
    {
        return $this->hasMany(\App\Models\TownHallAcknowledgement::class, 'townhall_communication_id');
    }

    public function hasBeenAcknowledgedBy($userId): bool
    {
        return $this->acknowledgements()->where('user_id', $userId)->exists();
    }
}
