<?php
// database yang menyimpan data navbar
// hanya author yang bisa mengubah navbar

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navbar_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');                           // Teks navigasi
            $table->string('url');                             // URL tujuan
            $table->string('icon')->nullable();                // Icon FontAwesome
            $table->unsignedInteger('sort_order')->default(0); // Urutan tampil
            $table->boolean('is_active')->default(true);       // Aktif/nonaktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navbar_items');
    }
};
