<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Makanan',
                'description' => 'Main dishes, appetizers, and desserts.',
            ],
            [
                'name'        => 'Minuman',
                'description' => 'Beverages, juices, and cocktails.',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
