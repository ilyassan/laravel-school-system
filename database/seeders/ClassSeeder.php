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
            'BAC-1',
            'BAC-2',
            'BAC-3',
            'BAC-4',
            'BAC-5',
        ];

        foreach ($classes as $className) {
            Classes::create(['name' => $className]);
        }
    }
}
