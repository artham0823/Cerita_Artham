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
                <img src="{{ asset(auth()->user()->avatar ?? 'img/profile.jpeg') }}" alt="Profile">
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
