<?php

// database yang menyimpan data chapter reactions

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapter_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade'); // chapter yang di reaksi
            $table->foreignId('user_id')->constrained()->onDelete('cascade');    // user yang memberi reaksi
            $table->string('reaction_type');                                     // e.g. 'like', 'love', 'cry', 'wow', 'laugh'
            $table->timestamps();
            
            // User hanya bisa memberikan satu reaction per chapter
            $table->unique(['chapter_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_reactions');
    }
};
