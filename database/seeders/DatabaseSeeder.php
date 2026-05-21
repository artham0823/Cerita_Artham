<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * =====================================================
 * SEEDER: DatabaseSeeder
 * =====================================================
 * Seeder utama — ini yang jalan pas php artisan db:seed.
 * Bikin data awal buat testing:
 * - 1 user author (pemilik web)
 * - 1 user admin
 * - 1 user member (buat testing)
 * - 1 cerita dari StorySeeder
 * =====================================================
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // bikin akun author — pemilik web, role tertinggi
        User::create([
            'name' => 'Artham',
            'username' => 'artham',
            'password' => bcrypt('artham0823'),
            'role' => 'author',
        ]);

        // bikin akun admin — bisa kelola konten, tapi bukan pemilik
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // bikin akun member — buat testing login member
        User::create([
            'name' => 'Member Test',
            'username' => 'member',
            'password' => bcrypt('member123'),
            'role' => 'member',
        ]);

        // panggil StorySeeder — bikin 1 cerita awal
        // panggil NavbarItemSeeder — bikin menu navigasi default
        $this->call([
            StorySeeder::class,
            NavbarItemSeeder::class,
        ]);
    }
}
