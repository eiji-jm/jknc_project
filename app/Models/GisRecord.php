<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GisRecord extends Model
{
    protected $fillable = [

        'uploaded_by',
        'submission_status',
        'receive_on',
        'period_date',

        'company_reg_no',
        'corporation_name',

        'annual_meeting',
        'meeting_type',

        'file'
    ];

    public function authorizedCapital()
    {
        return $this->hasMany(AuthorizedCapitalStock::class,'gis_id');
    }

    public function subscribedCapital()
    {
        return $this->hasMany(SubscribedCapital::class,'gis_id');
    }

    public function paidUpCapital()
    {
        return $this->hasMany(PaidUpCapital::class,'gis_id');
    }

    public function directors()
    {
        return $this->hasMany(DirectorOfficer::class,'gis_id');
    }

    public function stockholders()
    {
        return $this->hasMany(Stockholder::class,'gis_id');
    }
}