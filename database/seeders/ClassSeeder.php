<?php

namespace Database\Seeders;

use App\Models\Classes;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert default classes

        $numberOfClasses = 9;
        $classes = [];

        for ($i = 1; $i <= $numberOfClasses; $i++) {
            $classes[] = ['name' => 'BAC-' . $i];
        }

        Classes::insert($classes);
    }
}
