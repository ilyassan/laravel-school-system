<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        Role::insert([
            ['id' => UserRole::ADMIN, 'name' => 'admin'],
            ['id' => UserRole::TEACHER, 'name' => 'teacher'],
            ['id' => UserRole::STUDENT, 'name' => 'student'],
        ]);
    }
}
