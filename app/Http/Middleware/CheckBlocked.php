<?php

namespace App\Http\Middleware;

/**
 * =====================================================
 * MIDDLEWARE: CheckBlocked
 * =====================================================
 * Memeriksa apakah akun pengguna diblokir.
 * Jika diblokir, otomatis logout dan redirect ke login.
 * =====================================================
 */

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_blocked) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Akun Anda telah diblokir. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}
