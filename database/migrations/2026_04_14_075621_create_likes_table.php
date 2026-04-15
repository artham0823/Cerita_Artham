<?php

// database yang menyimpan data like pada cerita 
// kalo user login maka simpan user_id
// kalo guest maka simpan ip_address
// bisa unlike juga

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')                    // cerita yang dilike
                  ->constrained('stories')
                  ->onDelete('cascade');
            $table->foreignId('user_id')                     // user yang menyukai
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();    // IP address buat guest
            $table->timestamps();                            // waktu like dibuat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
