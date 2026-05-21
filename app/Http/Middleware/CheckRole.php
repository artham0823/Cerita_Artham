<?php

namespace App\Http\Middleware;

/**
 * =====================================================
 * MIDDLEWARE: CheckRole
 * =====================================================
 * Ini middleware buat ngecek role user sebelum masuk halaman.
 * Jadi kalo ada halaman khusus author/admin, user biasa
 * gak bakal bisa masuk. Mantap kan?
 *
 * Cara pakenya di route:
 * Route::middleware('role:author')->group(...)
 * Route::middleware('role:author,admin')->group(...)
 * =====================================================
 */

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * handle — fungsi utama middleware
     * ngecek apakah user udah login & rolenya sesuai
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // cek dulu, udah login belom? kalo belom, tendang ke login
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // cek role user — kalo gak sesuai, kasih 403 (forbidden)
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // lolos semua cek? lanjut ke halaman yang dituju
        return $next($request);
    }
}
