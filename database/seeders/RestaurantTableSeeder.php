<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestaurantTable;

class RestaurantTableSeeder extends Seeder
{
    /**
     * Seed the restaurant tables.
     */
    public function run(): void
    {
        $tables = [
            ['table_number' => '01', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => '02', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => '03', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => '04', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => '05', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => '06', 'capacity' => 6, 'status' => 'available'],
            ['table_number' => '07', 'capacity' => 6, 'status' => 'available'],
            ['table_number' => '08', 'capacity' => 8, 'status' => 'available'],
            ['table_number' => '09', 'capacity' => 4, 'status' => 'disabled'],
            ['table_number' => '10', 'capacity' => 10, 'status' => 'available'],
        ];

        foreach ($tables as $table) {
            RestaurantTable::firstOrCreate(
                ['table_number' => $table['table_number']],
                $table
            );
        }
    }
}
