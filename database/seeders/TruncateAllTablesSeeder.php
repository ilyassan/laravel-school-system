<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TruncateAllTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Get all tables
        $tables = DB::select('SHOW TABLES');

        // Truncate each table
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            DB::table($tableName)->truncate();
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
