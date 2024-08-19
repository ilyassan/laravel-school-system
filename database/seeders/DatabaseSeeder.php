<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TruncateAllTablesSeeder::class,
            RoleSeeder::class,
            ClassSeeder::class,
            SubjectSeeder::class,
            AdminSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            HomeworkSeeder::class,
            AbsenceSeeder::class,
            GradeSeeder::class,
            RatingSeeder::class,
            ReportSeeder::class,
            ChargeSeeder::class,
        ]);
    }
}
