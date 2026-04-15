<?php

// database yang menyimpan bab-bab dari setiap cerita

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')                 // kayak judul ceritanya
                  ->constrained('stories')
                  ->onDelete('cascade');
            $table->string('title');                      // judul babnya
            $table->text('content');                      // isi ceritanya
            $table->unsignedBigInteger('chapter_number'); // nomor urut babnya
            $table->timestamps();                         // tanggal dibuat dan diupdate
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
