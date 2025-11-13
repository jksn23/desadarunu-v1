<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@desadarunu.test')->doesntExist()) {
            User::create([
                'name' => 'Administrator Web',
                'email' => 'admin@desadarunu.test',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin_web',
                'email_verified_at' => now(),
            ]);
        }
    }
}
