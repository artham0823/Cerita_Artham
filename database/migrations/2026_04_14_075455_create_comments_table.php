<?php

// database yang menyimpan komentar-komentar dari setiap cerita

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')        // user yang berkomentar
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('chapter_id')     // chapter yang dikomentari
                  ->constrained('chapters')
                  ->onDelete('cascade');
            $table->text('content');            // isi komentar
            $table->timestamps();               // waktu komentar dibuat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
