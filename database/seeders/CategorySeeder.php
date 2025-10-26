<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "id" => 1,
                'title' => 'فريج الرعب',
                'description' => 'فريج الرعب هو قسم قوي',
                'image' => 'tech.jpg',
                'type' => 'normal',
                'end_at' => null,
                'views' => 100,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "id" => 2,
                'title' => 'الفقرات',
                'description' => 'الفقرات',
                'image' => 'tech.jpg',
                'type' => 'normal',
                'end_at' => null,
                'views' => 100,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data into the categories table
        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }
    }
}
