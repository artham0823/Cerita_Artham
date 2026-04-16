<?php

namespace App\Http\Middleware;

/**
 * =====================================================
 * MIDDLEWARE: CheckRole
 * =====================================================
 * Memverifikasi role pengguna sebelum mengakses halaman.
 * 
 * Cara pakai di route:
 * Route::middleware('role:author')->group(...)
 * Route::middleware('role:author,admin')->group(...)
 * =====================================================
 */

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah role user termasuk dalam role yang diizinkan
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
