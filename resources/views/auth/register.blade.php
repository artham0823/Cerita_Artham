{{-- 
    =====================================================
    VIEW: register.blade.php
    =====================================================
    Halaman registrasi — bikin akun member baru.
    Isi nama, username, password, terus konfirmasi password.
    Setelah daftar, langsung auto-login ke akun baru.
    ===================================================== 
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- title halaman --}}
    <title>Daftar - Ceritaku</title>
    {{-- font awesome buat icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- CSS utama --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{-- CSS auth khusus buat halaman register --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="light-theme">
    {{-- container halaman register — di tengah layar --}}
    <div class="auth-page">
        <div class="auth-card">
            {{-- logo + judul --}}
            <div class="auth-logo">
                <i class="fa-solid fa-book-open"></i>
                <h1>Ceritaku</h1>
                <p>Buat akun baru</p>
            </div>

            {{-- form register: nama + username + password + konfirmasi --}}
            <form method="POST" action="{{ route('register.process') }}">
                @csrf {{-- token CSRF — wajib biar aman --}}

                {{-- input nama lengkap --}}
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" autofocus required>
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- input username — harus unik, tanpa spasi --}}
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username (tanpa spasi)" required>
                    @error('username') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- input password — minimal 6 karakter --}}
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- konfirmasi password — harus sama kayak di atas --}}
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                </div>

                {{-- tombol submit daftar --}}
                <button type="submit" class="auth-submit">
                    <i class="fa-solid fa-user-plus"></i> Daftar
                </button>
            </form>

            {{-- link ke login + kembali ke beranda --}}
            <div class="auth-footer">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk</a><br><br>
                <a href="{{ route('home') }}" style="display:inline-block; margin-top:0.5rem; text-decoration:none; color:var(--text-muted);"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    {{-- script: cek tema --}}
    <script>
        // cek tema yg tersimpan di localStorage
        const saved = localStorage.getItem('ceritaku-theme');
        if (saved === 'dark') document.body.classList.replace('light-theme', 'dark-theme');
    </script>
</body>
</html>
