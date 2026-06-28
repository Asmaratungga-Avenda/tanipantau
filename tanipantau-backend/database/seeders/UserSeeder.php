<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@tanipantau.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        User::firstOrCreate(
            ['email' => 'petugas@tanipantau.com'],
            [
                'name' => 'Petugas User',
                'password' => Hash::make('password'),
                'role' => 'petugas'
            ]
        );

        User::firstOrCreate(
            ['email' => 'manajer@tanipantau.com'],
            [
                'name' => 'Manajer User',
                'password' => Hash::make('password'),
                'role' => 'manajer'
            ]
        );
    }
}
