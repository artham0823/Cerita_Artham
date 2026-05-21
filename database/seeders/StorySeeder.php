<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * =====================================================
 * SEEDER: StorySeeder
 * =====================================================
 * Bikin data cerita awal buat testing.
 * Cuma 1 cerita: "Rian" — ini data dummy aja.
 * Nanti bisa ditambahin lewat dashboard.
 * 
 * created_by = 1 artinya dibuat oleh user pertama (author)
 * =====================================================
 */
class StorySeeder extends Seeder
{
    public function run(): void
    {
        // insert cerita pertama ke tabel stories
        DB::table('stories')->insert([
            'title' => 'Rian',                    // judul cerita
            'description' => 'eak apala',          // deskripsi singkat
            'cover_image' => 'img/p2.jpg',         // gambar cover
            'genre' => 'Drama',                    // genre cerita
            'views_count' => 0,                    // views awal 0
            'is_featured' => true,                 // tampil di hero section
            'created_by' => 1,                     // dibuat oleh user ID 1 (author)
            'created_at' => now(),                 // timestamp
            'updated_at' => now(),                 // timestamp
        ]);
    }
}
