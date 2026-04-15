<?php

// database yang menyimpan riwayat bacaan user

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')        // user yang membaca
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('story_id')       // cerita yang dibaca
                  ->constrained('stories')
                  ->onDelete('cascade');
            $table->foreignId('chapter_id')     // chapter yang dibaca
                  ->constrained('chapters')
                  ->onDelete('cascade');
            $table->timestamp('read_at');               // waktu membaca
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_histories');
    }
};
