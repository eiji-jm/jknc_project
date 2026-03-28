<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@jknc.com'],
            [
                'name' => 'System Super Admin',
                'password' => Hash::make('jknc12345'),
                'role' => 'SuperAdmin',
            ]
        );
    }
}
