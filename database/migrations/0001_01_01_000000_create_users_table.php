<?php

// database yang menyimpan data user

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                                 // nama tampilan user
            $table->string('username')->unique();                                   // username unik untuk login
            $table->string('password');                                             // password user (hash)
            $table->enum('role', ['author', 'admin', 'member'])->default('member'); // role user
            $table->string('avatar')->nullable();                                   // foto profil user
            $table->string('title')->nullable();                                    // gelar user
            $table->text('bio')->nullable();                                        // bio user
            $table->boolean('is_blocked')->default(false);                          // status blokir user
            $table->rememberToken();                                                // token untuk remember me (ingat saya)
            $table->timestamps();                                                   // created_at & updated_at
        });

        // tabel untuk reset password (bawaan laravel)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // tabel session buat nyimpen data pengguna
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
