<?php

// migration buat tabel story_requests — tempat nyimpen request cerita dari member

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // bikin tabel story_requests
        Schema::create('story_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siapa yang request
            $table->string('title');                                           // judul cerita yang diminta
            $table->text('description')->nullable();                          // deskripsi opsional
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // status request
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_requests');
    }
};
