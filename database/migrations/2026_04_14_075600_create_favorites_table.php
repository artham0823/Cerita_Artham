<?php

// database yang menyimpan cerita favorit user seperti (1 kali percerita)
// author, admin, member 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')             // user yang memfavoritkan
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('story_id')            // cerita yang difavoritkan
                  ->constrained('stories')
                  ->onDelete('cascade');
            $table->timestamps();                    // waktu favorit dibuat
            $table->unique(['user_id', 'story_id']); // memastikan 1 user hanya bisa favorit 1 cerita sekali
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
