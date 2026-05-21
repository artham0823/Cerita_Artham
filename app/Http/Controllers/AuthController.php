<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: AuthController (Autentikasi)
 * =====================================================
 * Controller ini ngurusin semua hal soal login,
 * register, sama logout. Simpel tapi penting banget!
 * 
 * - Login  : semua user bisa masuk (username + password)
 * - Register: bikin akun member baru (publik)
 * - Logout : keluar dari akun, sesi dihancurin
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * showLogin — nampilin halaman form login
     * kalo udah login, langsung redirect ke home aja
     */
    public function showLogin()
    {
        // udah login? gak usah ke login lagi, balik ke home
        if (Auth::check()) {
            return redirect('/');
        }

        // belom login? tampilin form login
        return view('auth.login');
    }

    /**
     * login — proses login user
     * cek username ada gak, cek diblokir gak, cek password bener gak
     */
    public function login(Request $request)
    {
        // validasi input — username sama password wajib diisi
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // cari user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // username gak ketemu? kasih error
        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan.'])->withInput();
        }

        // cek apakah akunnya diblokir
        if ($user->is_blocked) {
            return back()->withErrors(['username' => 'Akun Anda telah diblokir.'])->withInput();
        }

        // coba login — kalo password bener, masuk!
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) {
            // regenerate session biar aman dari session fixation attack
            $request->session()->regenerate();

            // redirect ke home + kasih pesan selamat datang
            return redirect()->intended('/')->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        // password salah? kasih error
        return back()->withErrors(['password' => 'Password salah.'])->withInput();
    }

    /**
     * showRegister — nampilin form registrasi
     * kalo udah login, gak bisa register lagi
     */
    public function showRegister()
    {
        // udah login? balik ke home
        if (Auth::check()) {
            return redirect('/');
        }

        // tampilin form register
        return view('auth.register');
    }

    /**
     * register — proses bikin akun baru
     * otomatis jadi member, langsung login setelah daftar
     */
    public function register(Request $request)
    {
        // validasi input — nama, username, password, konfirmasi password
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, dan underscore.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // cegah siapapun bikin akun dengan username "artham"
        // soalnya itu username khusus author/pemilik
        if (strtolower($request->username) === 'artham') {
            return back()->withErrors(['username' => 'Username ini tidak boleh digunakan.'])->withInput();
        }

        // bikin user baru — role otomatis "member"
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password, // otomatis di-hash oleh cast di model User
            'role' => 'member',
        ]);

        // langsung login setelah daftar — biar gak usah login lagi
        Auth::login($user);

        // redirect ke home + kasih ucapan selamat
        return redirect('/')->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name . '!');
    }

    /**
     * logout — proses keluar dari akun
     * session dihancurin, token diregenerasi
     */
    public function logout(Request $request)
    {
        // logout dari akun
        Auth::logout();

        // hancurin session biar bersih
        $request->session()->invalidate();

        // regenerate CSRF token biar aman
        $request->session()->regenerateToken();

        // redirect ke home + kasih pesan berhasil
        return redirect('/')->with('success', 'Berhasil keluar dari akun.');
    }
}
