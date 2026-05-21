<?php

namespace App\Http\Middleware;

/**
 * =====================================================
 * MIDDLEWARE: CheckBlocked
 * =====================================================
 * Middleware ini otomatis jalan di tiap request web.
 * Fungsinya: kalo user yg login ternyata akunnya diblokir,
 * langsung auto-logout terus redirect ke login.
 * Jadi gak bisa akses apa-apa lagi deh 😤
 * =====================================================
 */

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlocked
{
    /**
     * handle — cek apakah user diblokir
     * kalo iya, langsung tendang keluar
     */
    public function handle(Request $request, Closure $next): Response
    {
        // cek: user login DAN akunnya diblokir?
        if (auth()->check() && auth()->user()->is_blocked) {
            // auto logout — session dihancurin
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // redirect ke login + kasih pesan error
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah diblokir. Silakan hubungi administrator.');
        }

        // kalo gak diblokir, lanjut aja
        return $next($request);
    }
}
