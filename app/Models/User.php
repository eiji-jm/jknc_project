<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    public function userPermission()
    {
        return $this->hasOne(\App\Models\UserPermission::class);
    }

    public function hasPermission(string $permission): bool
    {
        $userPermission = $this->userPermission;

        if ($userPermission && isset($userPermission->{$permission})) {
            return (bool) $userPermission->{$permission};
        }

        $rolePermission = \App\Models\RolePermission::where('role', $this->role)->first();

        return $rolePermission ? (bool) $rolePermission->{$permission} : false;
    }
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
