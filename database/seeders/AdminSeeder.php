<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'angelina@gmail.com'],
            [
                'name' => 'Admin Nanda',
                'password' => Hash::make('angel12'),
                'role' => 'admin',
            ]
        );
    }
}
