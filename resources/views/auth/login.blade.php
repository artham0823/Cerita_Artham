{{-- 
    =====================================================
    VIEW: login.blade.php
    =====================================================
    Halaman login — user masukin username + password buat masuk.
    Pake layout standalone (gak pake layout app/dashboard),
    soalnya halaman login emang berdiri sendiri.
    ===================================================== 
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- title halaman — biar SEO friendly --}}
    <title>Masuk - Ceritaku</title>
    {{-- icon font awesome buat icon2 keren --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- CSS utama (variabel warna, font, reset) --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{-- CSS khusus halaman auth (login/register) --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="light-theme">
    {{-- toast error — muncul kalo ada session error --}}
    @if(session('error'))
        <div class="toast toast-error" id="toast">{{ session('error') }}</div>
    @endif

    {{-- container utama login — centered di tengah layar --}}
    <div class="auth-page">
        <div class="auth-card">
            {{-- logo + judul --}}
            <div class="auth-logo">
                <i class="fa-solid fa-book-open"></i>
                <h1>Ceritaku</h1>
                <p>Masuk ke akunmu</p>
            </div>

            {{-- form login: username + password --}}
            <form method="POST" action="{{ route('login.process') }}">
                @csrf {{-- token CSRF — wajib buat keamanan form --}}

                {{-- input username --}}
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" autofocus required>
                    {{-- error message kalo username salah --}}
                    @error('username') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- input password --}}
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    {{-- error message kalo password salah --}}
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- checkbox "ingat saya" — biar gak perlu login ulang --}}
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                </div>

                {{-- tombol submit login --}}
                <button type="submit" class="auth-submit">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk
                </button>
            </form>

            {{-- link ke register + kembali ke beranda --}}
            <div class="auth-footer">
                Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a><br><br>
                <a href="{{ route('home') }}" style="display:inline-block; margin-top:0.5rem; text-decoration:none; color:var(--text-muted);"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    {{-- script: cek tema yang disimpan di localStorage --}}
    <script>
        // ambil tema yang tersimpan — kalo dark, ganti class body
        const saved = localStorage.getItem('ceritaku-theme');
        if (saved === 'dark') document.body.classList.replace('light-theme', 'dark-theme');
        // auto-hapus toast setelah 5 detik
        setTimeout(() => { const t = document.getElementById('toast'); if(t) t.remove(); }, 5000);
    </script>
</body>
</html>
