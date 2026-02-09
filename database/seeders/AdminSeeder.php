<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@arabmenus.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin1234'),
                'is_admin' => true,
            ]
        );
    }
}
