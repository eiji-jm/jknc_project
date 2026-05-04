<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $fillable = [
        'user_id',
        'manage_users',
        'access_admin_dashboard',
        'approve_townhall',
        'create_townhall',
        'create_corporate',
        'approve_corporate',
        'access_townhall',
        'access_corporate',
        'access_activities',
        'access_contacts',
        'access_company',
        'access_transmittal',
        'access_deals',
        'access_services',
        'access_project',
        'access_regular',
        'access_product',
        'access_policies',
        'create_sales_marketing',
        'approve_sales_marketing',
        'access_sales_marketing',
        'approve_policies',
        'access_human_capital',
    ];
}