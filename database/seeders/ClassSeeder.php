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
        $classes = [
            '2BAC-1',
            '2BAC-2',
            '2BAC-3',
            '2BAC-4',
        ];

        foreach ($classes as $className) {
            Classes::create(['name' => $className]);
        }
    }
}
