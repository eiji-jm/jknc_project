<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = [
        'role',
        'manage_users',
        'access_admin_dashboard',
        'create_townhall',
        'approve_townhall',
        'create_corporate',
        'approve_corporate',
        'access_townhall',
        'access_corporate',
        'access_activities',
        'access_contacts',
        'access_company',
    ];
}
