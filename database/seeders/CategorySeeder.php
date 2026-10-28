<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'iPhone 16 Series',
                'slug' => 'iphone-16-series',
                'icon' => 'sparkles',
            ],
            [
                'name' => 'iPhone 15 Series',
                'slug' => 'iphone-15-series',
                'icon' => 'device-phone-mobile',
            ],
            [
                'name' => 'iPhone 14 Series',
                'slug' => 'iphone-14-series',
                'icon' => 'device-phone-mobile',
            ],
            [
                'name' => 'iPhone 13 Series',
                'slug' => 'iphone-13-series',
                'icon' => 'device-phone-mobile',
            ],
            [
                'name' => 'Pre-Owned / Second Berkualitas',
                'slug' => 'pre-owned-second-berkualitas',
                'icon' => 'shield-check',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
