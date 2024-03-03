<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '0666666666',
            'password' => Hash::make('password'),
            'gender' => 'M',
            'role_id' => UserRole::ADMIN,
            'remember_token' => Str::random(10),
        ]);
    }
}
