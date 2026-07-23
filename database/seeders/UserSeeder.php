<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@elevage.local'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('Lapin@2026'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
