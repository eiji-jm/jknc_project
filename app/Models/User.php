<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    public function userPermission()
    {
        return $this->hasOne(\App\Models\UserPermission::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'SuperAdmin') {
            return true;
        }

        if (!Schema::hasTable('user_permissions') || !Schema::hasTable('role_permissions')) {
            return $this->fallbackPermission($permission);
        }

        $userPermission = $this->userPermission;
        if ($userPermission && isset($userPermission->{$permission})) {
            return (bool) $userPermission->{$permission};
        }

        $rolePermission = \App\Models\RolePermission::where('role', $this->role)->first();

        return $rolePermission ? (bool) $rolePermission->{$permission} : false;
    }

    private function fallbackPermission(string $permission): bool
    {
        if ($this->role === 'Admin') {
            return in_array($permission, [
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
            ], true);
        }

        return in_array($permission, [
            'access_townhall',
            'access_corporate',
            'access_activities',
            'access_contacts',
            'access_company',
            'create_townhall',
            'create_corporate',
        ], true);
    }
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
