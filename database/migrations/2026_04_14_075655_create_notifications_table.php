<?php

// database yang menyimpan data notifikasi
// memiliki limit notifikasi (hanya autho (100) dan admin (50))
// jika melebihi limit maka hapus notifikasi terlama
// jenis notifikasi: cerita ditambahkan/diubah/dihapus, chapter ditambahkan/diubah/dihapus,
// komentar baru, request cerita baru, akun dibuat/diubah/diblokir

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')                     // Pemilik notifikasi
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('type');                           // Jenis notifikasi
            $table->text('message');                          // Pesan notifikasi
            $table->string('actor_username')->nullable();     // Siapa yang melakukan aksi
            $table->timestamp('created_at')->nullable();      // Waktu notifikasi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
