{{-- 
    =====================================================
    LAYOUT: dashboard.blade.php
    =====================================================
    Layout utama dashboard — semua halaman dashboard pake ini.
    Isinya: sidebar navigasi di kanan + area konten di kiri.
    
    Fitur:
    - Sidebar responsive (di HP bisa toggle buka/tutup)
    - Navigasi berdasarkan role (author/admin/member)
    - Dark/Light theme dari localStorage
    - Toast notifications buat feedback
    - Overlay pas sidebar kebuka di mobile
    ===================================================== 
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- title dinamis — tiap halaman bisa override --}}
    <title>@yield('title', 'Dashboard - Ceritaku')</title>
    {{-- font awesome buat icon-icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- CSS utama — variabel warna, font, tombol, dll --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{-- CSS dashboard base — sidebar, stats, tabel, dll --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    {{-- CSS khusus per role — beda warna aksen per role --}}
    @if(auth()->user()->isAuthor())
        <link rel="stylesheet" href="{{ asset('css/dashboard-author.css') }}">
    @elseif(auth()->user()->isAdmin())
        <link rel="stylesheet" href="{{ asset('css/dashboard-admin.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/dashboard-member.css') }}">
    @endif
    {{-- slot buat tambahin CSS dari child view --}}
    @stack('styles')
</head>
<body class="light-theme">
    {{-- TOAST — notifikasi sukses/error yang nongol di atas kanan --}}
    @if(session('success'))
        <div class="toast toast-success" id="toast">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="toast toast-error" id="toast">{{ session('error') }}</div>
    @endif

    {{-- LAYOUT UTAMA: sidebar + konten --}}
    <div class="dash-layout">
        {{-- ==================== SIDEBAR ==================== --}}
        <aside class="dash-sidebar" id="dash-sidebar">
            {{-- header sidebar — logo --}}
            <div class="dash-sidebar-header">
                <a href="{{ route('home') }}" class="logo">
                    <i class="fa-solid fa-book-open"></i> Ceritaku
                </a>
            </div>

            {{-- info user yang login — avatar + nama + role --}}
            <div class="dash-sidebar-user">
                {{-- avatar user — kalo belum punya, pake default --}}
                <img src="{{ asset(auth()->user()->avatar ?? 'img/p2.jpg') }}" alt="Avatar">
                <div class="user-info">
                    <h4>{{ auth()->user()->name }}</h4>
                    {{-- badge role — warna beda per role --}}
                    <span class="role-badge-{{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>

            {{-- navigasi sidebar — menu-menu dashboard --}}
            <nav class="dash-sidebar-nav">
                {{-- menu umum: semua role bisa akses --}}
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear"></i> Profil
                </a>

                {{-- menu khusus author/admin: kelola konten --}}
                @if(auth()->user()->canManageContent())
                    <div class="nav-section">Konten</div>
                    <a href="{{ route('dashboard.stories.index') }}" class="{{ request()->routeIs('dashboard.stories.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-book"></i> Kelola Cerita
                    </a>
                @endif

                {{-- menu khusus author only: pengaturan --}}
                @if(auth()->user()->isAuthor())
                    <div class="nav-section">Pengaturan</div>
                    <a href="{{ route('dashboard.navbar.index') }}" class="{{ request()->routeIs('dashboard.navbar.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-compass"></i> Kelola Navbar
                    </a>
                    <a href="#" class="">
                        <i class="fa-solid fa-bell"></i> Notifikasi
                    </a>
                @endif
            </nav>

            {{-- footer sidebar: tombol ke beranda + logout --}}
            <div class="dash-sidebar-footer">
                <a href="{{ route('home') }}" class="btn btn-outline" style="width:100%;justify-content:center;margin-bottom:0.8rem">
                    <i class="fa-solid fa-home"></i> Ke Beranda
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- OVERLAY — muncul di mobile pas sidebar kebuka, klik buat tutup --}}
        <div class="dash-sidebar-overlay" id="dash-sidebar-overlay"></div>

        {{-- ==================== KONTEN UTAMA ==================== --}}
        <main class="dash-content">
            {{-- konten dari child view masuk sini --}}
            @yield('content')
        </main>
    </div>

    {{-- TOMBOL TOGGLE MOBILE — di pojok kiri bawah, buat buka sidebar di HP --}}
    <button class="dash-mobile-toggle" id="dash-mobile-toggle" aria-label="Toggle Menu">
        <i class="fa-solid fa-bars" id="toggle-icon"></i>
    </button>

    {{-- ==================== JAVASCRIPT ==================== --}}
    <script>
        // --- TEMA ---
        // cek tema yang tersimpan di localStorage, kalo dark ganti class
        const saved = localStorage.getItem('ceritaku-theme');
        if (saved === 'dark') document.body.classList.replace('light-theme', 'dark-theme');

        // --- SIDEBAR TOGGLE (mobile) ---
        // buka/tutup sidebar pake tombol toggle + overlay
        (function() {
            const toggle = document.getElementById('dash-mobile-toggle');       // tombol toggle
            const dashSidebar = document.getElementById('dash-sidebar');        // elemen sidebar
            const overlay = document.getElementById('dash-sidebar-overlay');    // overlay gelap
            const toggleIcon = document.getElementById('toggle-icon');          // icon di tombol

            // fungsi buka sidebar — tambahin class active
            function openSidebar() {
                dashSidebar.classList.add('active');
                overlay.classList.add('active');
                // ganti icon dari hamburger ke X
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-bars');
                    toggleIcon.classList.add('fa-xmark');
                }
            }

            // fungsi tutup sidebar — hapus class active
            function closeSidebar() {
                dashSidebar.classList.remove('active');
                overlay.classList.remove('active');
                // ganti icon balik ke hamburger
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-xmark');
                    toggleIcon.classList.add('fa-bars');
                }
            }

            // klik toggle: buka/tutup sidebar
            if (toggle) {
                toggle.addEventListener('click', () => {
                    if (dashSidebar.classList.contains('active')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            // klik overlay: tutup sidebar
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // pencet ESC: tutup sidebar
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && dashSidebar.classList.contains('active')) {
                    closeSidebar();
                }
            });

            // klik menu link di sidebar (mobile): auto-tutup sidebar
            const navLinks = dashSidebar.querySelectorAll('.dash-sidebar-nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });
        })();

        // --- TOAST ---
        // auto-hapus toast notifikasi setelah 5 detik
        setTimeout(() => { const t = document.getElementById('toast'); if(t) t.remove(); }, 5000);
    </script>
    {{-- slot buat tambahin script dari child view --}}
    @stack('scripts')
</body>
</html>
