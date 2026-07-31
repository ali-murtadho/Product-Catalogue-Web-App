<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'sort_order' => 1],
            ['name' => 'Fashion', 'sort_order' => 2],
            ['name' => 'Makanan & Minuman', 'sort_order' => 3],
            ['name' => 'Kesehatan & Kecantikan', 'sort_order' => 4],
            ['name' => 'Rumah Tangga', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'sort_order' => $category['sort_order'],
                ]
            );
        }
    }
}
