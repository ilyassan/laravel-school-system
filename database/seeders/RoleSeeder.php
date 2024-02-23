<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        Role::create([
            'id' => UserRole::ADMIN,
            'name' => 'admin',
        ]);
        
        Role::create([
            'id' => UserRole::TEACHER,
            'name' => 'teacher',
        ]);
        
        Role::create([
            'id' => UserRole::STUDENT,
            'name' => 'student',
        ]);
        
    }
}
