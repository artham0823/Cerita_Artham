<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ceritaku - Platform membaca dan membagi kisah yang terinspirasi dari hidup.">
    <title>@yield('title', 'Ceritaku - Platform Cerita Digital')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="light-theme">

    {{-- Toast Notifications --}}
    @if(session('success'))
        <div class="toast toast-success" id="toast">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="toast toast-error" id="toast">{{ session('error') }}</div>
    @endif

    {{-- Sidebar Overlay (untuk mobile) --}}
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('home') }}" class="logo">
                <i class="fa-solid fa-book-open"></i> Ceritaku
            </a>
            <button class="sidebar-close-btn" id="sidebar-close-btn" aria-label="Tutup Menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <ul>
                @php $navItems = \App\Models\NavbarItem::active()->get(); @endphp
                @foreach($navItems as $nav)
                    <li>
                        <a href="{{ $nav->url }}" class="{{ request()->is(ltrim($nav->url, '/') ?: '/') ? 'active' : '' }}">
                            <i class="{{ $nav->icon }}"></i> {{ $nav->label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
        <div class="sidebar-bottom">
            <button id="sidebar-theme-toggle" class="theme-btn" aria-label="Toggle Theme">
                <i class="fa-solid fa-moon"></i> <span>Mode Gelap</span>
            </button>
            @auth
                <a href="{{ route('dashboard') }}" class="sidebar-profile">
                    <img src="{{ asset(auth()->user()->avatar ?? 'img/p2.jpg') }}" alt="Profile">
                    <span>{{ auth()->user()->name }}</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="sidebar-profile">
                    <i class="fa-solid fa-right-to-bracket" style="font-size:1.2rem;color:var(--primary-color)"></i>
                    <span>Masuk</span>
                </a>
            @endauth
        </div>
    </aside>

    {{-- Header Navigation --}}
    <header class="navbar" id="main-navbar">
        <button class="hamburger-btn" id="hamburger-btn" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="{{ route('home') }}" class="logo desktop-logo">
            <i class="fa-solid fa-book-open"></i> Ceritaku
        </a>
        <nav class="desktop-nav">
            <ul>
                @foreach($navItems as $nav)
                    <li>
                        <a href="{{ $nav->url }}" class="{{ request()->is(ltrim($nav->url, '/') ?: '/') ? 'active' : '' }}">
                            {{ $nav->label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
        <div class="nav-actions">
            <form action="{{ route('search') }}" method="GET" class="search-bar">
                <input type="text" name="q" placeholder="Cari cerita favoritmu..." value="{{ request('q') }}">
                <button type="submit"><i class="fa-solid fa-search"></i></button>
            </form>
            <button id="theme-toggle" class="theme-btn desktop-only" aria-label="Toggle Theme">
                <i class="fa-solid fa-moon"></i>
            </button>
            @auth
                <div class="nav-user-menu desktop-only">
                    <div class="user-profile">
                        <img src="{{ asset(auth()->user()->avatar ?? 'img/p2.jpg') }}" alt="Profile">
                    </div>
                    <div class="nav-user-dropdown">
                        <a href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                        <a href="{{ route('profile.edit') }}"><i class="fa-solid fa-user-gear"></i> Profil</a>
                        <div class="divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary desktop-only" style="padding:0.5rem 1.2rem;font-size:0.9rem">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk
                </a>
            @endauth
        </div>
    </header>

    {{-- Main Content --}}
    @yield('content')

    {{-- Request Cerita Section (di atas footer) --}}
    @if(!isset($hideRequest))
    <section class="request-section">
        <div class="container">
            <h3><i class="fa-solid fa-lightbulb"></i> Punya Ide Cerita?</h3>
            <p>Kirimkan permintaan ceritamu dan siapa tahu akan segera ditulis!</p>
            @auth
                <form action="{{ route('story-request.store') }}" method="POST" class="request-form">
                    @csrf
                    <input type="text" name="title" placeholder="Judul cerita yang kamu inginkan..." required>
                    <textarea name="description" placeholder="Deskripsi singkat (opsional)..."></textarea>
                    <button type="submit"><i class="fa-solid fa-paper-plane"></i> Kirim Request</button>
                </form>
            @else
                <p><a href="{{ route('login') }}" style="color:white;text-decoration:underline;font-weight:600">Login</a> atau <a href="{{ route('register') }}" style="color:white;text-decoration:underline;font-weight:600">buat akun</a> untuk mengirim request cerita.</p>
            @endauth
        </div>
    </section>
    @endif

    {{-- Footer --}}
    <footer id="main-footer">
        <div class="container footer-content">
            <div class="footer-brand">
                <i class="fa-solid fa-book-open"></i> Ceritaku
                <p>Platform membaca dan membagi kisah yang terinspirasi dari hidup.</p>
            </div>
            <div class="footer-links">
                <ul>
                    <li><strong>Eksplorasi</strong></li>
                    <li><a href="{{ route('explore') }}">Jelajahi</a></li>
                    <li><a href="{{ route('popular') }}">Populer</a></li>
                    <li><a href="{{ route('search') }}">Cari Cerita</a></li>
                </ul>
                <ul>
                    <li><strong>Akun</strong></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('profile.edit') }}">Profil</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Masuk</a></li>
                        <li><a href="{{ route('register') }}">Daftar</a></li>
                    @endauth
                </ul>
                <ul>
                    <li><strong>Hubungi Author</strong></li>
                    <li><a href="https://wa.me/6285707298084" target="_blank" title="085707298084"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></li>
                    <li><a href="https://instagram.com/artham_.26" target="_blank"><i class="fa-brands fa-instagram"></i> Instagram (@artham_.26)</a></li>
                    <li><a href="https://tiktok.com/@ad_ryuu" target="_blank"><i class="fa-brands fa-tiktok"></i> TikTok (@ad_ryuu)</a></li>
                    <li><span style="color:var(--text-muted);font-size:0.95rem;display:flex;align-items:center;gap:0.5rem;"><i class="fa-brands fa-discord"></i> Discord: artham_26</span></li>
                    <li style="margin-top: 0.5rem;">
                        <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Scan WA:</span>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://wa.me/6285707298084" alt="WA Barcode" style="border-radius: 8px; box-shadow: var(--shadow-sm); border: 2px solid white;">
                    </li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; {{ date('Y') }} Ceritaku. All rights reserved.</p>
        </div>
    </footer>

    {{-- Anti-Copy & Theme & Sidebar JS --}}
    <script>
        // Theme toggle
        const themeBtn = document.getElementById('theme-toggle');
        const sidebarThemeBtn = document.getElementById('sidebar-theme-toggle');
        const body = document.body;

        const savedTheme = localStorage.getItem('ceritaku-theme');
        if (savedTheme === 'dark') {
            body.classList.replace('light-theme', 'dark-theme');
            updateThemeIcons(true);
        }

        function toggleTheme() {
            const isDark = body.classList.contains('light-theme');
            if (isDark) {
                body.classList.replace('light-theme', 'dark-theme');
                localStorage.setItem('ceritaku-theme', 'dark');
            } else {
                body.classList.replace('dark-theme', 'light-theme');
                localStorage.setItem('ceritaku-theme', 'light');
            }
            updateThemeIcons(isDark);
        }

        function updateThemeIcons(isDark) {
            if (themeBtn) {
                const icon = themeBtn.querySelector('i');
                icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
            if (sidebarThemeBtn) {
                const icon = sidebarThemeBtn.querySelector('i');
                const text = sidebarThemeBtn.querySelector('span');
                icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
                if (text) text.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
            }
        }

        if (themeBtn) themeBtn.addEventListener('click', toggleTheme);
        if (sidebarThemeBtn) sidebarThemeBtn.addEventListener('click', toggleTheme);

        // Sidebar
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');
        const sidebarCloseBtn = document.getElementById('sidebar-close-btn');

        function openSidebar() { sidebar.classList.add('active'); sidebarBackdrop.classList.add('active'); body.style.overflow = 'hidden'; }
        function closeSidebar() { sidebar.classList.remove('active'); sidebarBackdrop.classList.remove('active'); body.style.overflow = ''; }

        if (hamburgerBtn) hamburgerBtn.addEventListener('click', openSidebar);
        if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
        if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);

        // Profile Dropdown Toggle
        const profileImg = document.querySelector('.nav-user-menu img');
        const userDropdown = document.querySelector('.nav-user-dropdown');
        if (profileImg && userDropdown) {
            profileImg.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });
            document.addEventListener('click', (e) => {
                if (!userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('show');
                }
            });
        }

        // Anti-copy pada area cerita
        document.querySelectorAll('.no-copy').forEach(el => {
            el.addEventListener('contextmenu', e => e.preventDefault());
            el.addEventListener('copy', e => e.preventDefault());
        });
        document.addEventListener('keydown', function(e) {
            if (document.querySelector('.no-copy:hover')) {
                if ((e.ctrlKey || e.metaKey) && ['c','a','u','s'].includes(e.key.toLowerCase())) {
                    e.preventDefault();
                }
            }
        });

        // Auto-remove toast
        setTimeout(() => { const t = document.getElementById('toast'); if(t) t.remove(); }, 5000);
    </script>
    @stack('scripts')
</body>
</html>
