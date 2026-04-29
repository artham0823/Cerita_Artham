<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stories')->insert([
            'title' => 'Rian',
            'description' => 'eak apala',
            'cover_image' => 'img/p1.jpg',
            'genre' => 'Drama',
            'views_count' => 0,
            'is_featured' => true,
            'created_by' => 1,
        ]);
    }
}
