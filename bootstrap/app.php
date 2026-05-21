<?php

// bootstrap/app.php — konfigurasi utama aplikasi Laravel
// disini kita daftarin middleware custom buat cek role & blocked

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // daftarin middleware custom — biar bisa dipake di route
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,           // cek role user (author/admin/member)
            'check.blocked' => \App\Http\Middleware\CheckBlocked::class, // cek apakah user diblokir
        ]);

        // CheckBlocked jalan otomatis di semua request web
        // jadi kalo user diblokir, langsung auto-logout
        $middleware->web(append: [
            \App\Http\Middleware\CheckBlocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

