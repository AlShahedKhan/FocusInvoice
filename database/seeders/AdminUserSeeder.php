<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'invite_code' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'), // Hash the password before saving
            'is_admin' => true
        ]);
    }
}
