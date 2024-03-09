<?php

namespace Database\Seeders;

use App\Models\Classes;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert default classes

        $numberOfClasses = 12;
        $classes = [];

        for ($i = 1; $i <= $numberOfClasses; $i++) {
            $classes[] = ['name' => 'BAC-' . $i];
        }

        Classes::insert($classes);
    }
}
