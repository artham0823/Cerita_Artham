<?php

// database yang menyimpan judul cerita

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');                               // judul cerita
            $table->text('description')->nullable();               // deskripsi singkat
            $table->string('cover_image')->nullable();             // gambar cover buat cerita
            $table->string('genre')->nullable();                   // genre cerita
            $table->unsignedBigInteger('views_count')->default(0); // jumlah viewer
            $table->unsignedBigInteger('likes_count')->default(0); // jumlah like
            $table->boolean('is_featured')->default(false);        // ditampilkan di hero section
            $table->foreignId('created_by')                        // pembuat cerita
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();                                  // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
