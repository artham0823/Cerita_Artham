{{-- 
    =====================================================
    VIEW: dashboard/index.blade.php
    =====================================================
    Halaman utama dashboard — tampilan beda per role:
    - Author: stats lengkap + grafik + komentar + notifikasi
    - Admin: stats tanpa admin count + komentar + notifikasi
    - Member: stats baca + favorit + riwayat bacaan
    
    Data awalnya dari seeder (cerita "Rian"), jadi
    kebanyakan bakal kosong. Nanti seiring pake app,
    datanya bakal nambah.
    ===================================================== 
--}}
@extends('layouts.dashboard')
@section('title', 'Dashboard - Ceritaku')

@section('content')
{{-- header dashboard — sapaan + info role --}}
<div class="dash-header">
    <div>
        {{-- sapaan pake nama user yang login --}}
        <h1>Selamat Datang, {{ $user->name }}!</h1>
        {{-- info role user --}}
        <p>{{ $user->isAuthor() ? 'Dashboard Author — Otoritas Tertinggi' : ($user->isAdmin() ? 'Dashboard Admin' : 'Dashboard Member') }}</p>
    </div>
</div>

{{-- ==================== DASHBOARD AUTHOR & ADMIN ==================== --}}
{{-- statistik cuma muncul buat author/admin (yang bisa kelola konten) --}}
@if($user->canManageContent())
<div class="stats-grid {{ $user->isAuthor() ? 'author-dashboard' : 'admin-dashboard' }}">
    {{-- kartu: total cerita --}}
    <a href="{{ route('dashboard.stories.index') }}" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
        <div class="stat-value">{{ $totalStories }}</div>
        <div class="stat-label">Total Cerita</div>
    </a>
    {{-- kartu: total bab/chapter --}}
    <a href="{{ route('dashboard.stories.index') }}" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
        <div class="stat-value">{{ $totalChapters }}</div>
        <div class="stat-label">Total Bab</div>
    </a>
    {{-- kartu: total komentar --}}
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-comments"></i></div>
        <div class="stat-value">{{ $totalComments }}</div>
        <div class="stat-label">Total Komentar</div>
    </div>
    {{-- kartu: total member --}}
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-value">{{ $totalMembers }}</div>
        <div class="stat-label">Total Member</div>
    </div>
    {{-- kartu: total admin (cuma author yang bisa liat) --}}
    @if($user->isAuthor() && isset($totalAdmins))
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div>
        <div class="stat-value">{{ $totalAdmins }}</div>
        <div class="stat-label">Total Admin</div>
    </div>
    @endif
</div>

{{-- grafik statistik — cuma buat author --}}
@if($user->isAuthor() && isset($genreLabels))
<div class="dash-card" style="margin-top: 1rem;">
    <h3><i class="fa-solid fa-chart-pie"></i> Analisis Data Ceritaku</h3>
    {{-- grid 2 kolom: grafik line + doughnut --}}
    <div class="charts-grid">
        <div class="chart-container">
            {{-- canvas buat grafik aktivitas baca --}}
            <canvas id="readingChart"></canvas>
        </div>
        <div class="chart-container">
            {{-- canvas buat grafik genre --}}
            <canvas id="genreChart"></canvas>
        </div>
    </div>
</div>

{{-- Chart.js CDN + konfigurasi grafik --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- GRAFIK AKTIVITAS MEMBACA (7 hari terakhir) ---
    // tipe: line chart — garis naik/turun sesuai jumlah bab yang dibaca
    const readingCtx = document.getElementById('readingChart').getContext('2d');
    new Chart(readingCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($readingLabels ?? []) !!}, // tanggal 7 hari terakhir
            datasets: [{
                label: 'Jumlah Bab Dibaca',
                data: {!! json_encode($readingData ?? []) !!}, // jumlah bab per hari
                borderColor: '#e28743',          // warna garis: orange
                backgroundColor: 'rgba(226, 135, 67, 0.2)', // area di bawah garis
                borderWidth: 3,
                tension: 0.3,   // kelengkungan garis (0 = lurus, 1 = melengkung banget)
                fill: true      // isi area di bawah garis
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: 'Aktivitas Membaca 7 Hari Terakhir', color: '#8b7a65' }
            }
        }
    });

    // --- GRAFIK DISTRIBUSI GENRE ---
    // tipe: doughnut — lingkaran buat liat perbandingan genre
    const genreCtx = document.getElementById('genreChart').getContext('2d');
    new Chart(genreCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($genreLabels ?? []) !!}, // nama-nama genre
            datasets: [{
                data: {!! json_encode($genreData ?? []) !!}, // jumlah cerita per genre
                backgroundColor: [
                    '#e28743', '#8b7a65', '#d1bfae', '#f4ecd8', '#6b5c4d' // warna per slice
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: 'Distribusi Genre', color: '#8b7a65' }
            }
        }
    });
});
</script>
@endif

{{-- daftar cerita terbaru (ringkasan) --}}
<div class="dash-card">
    <h3><i class="fa-solid fa-book"></i> Cerita Terbaru</h3>
    @if(isset($recentStories) && $recentStories->count() > 0)
        @foreach($recentStories as $story)
            <div style="padding:0.6rem 0;border-bottom:1px solid var(--border-color);font-size:0.95rem">
                <strong>{{ $story->title }}</strong>
                <small style="color:var(--text-muted);display:block;margin-top:0.2rem">
                    Genre: {{ $story->genre }} — Views: {{ number_format($story->views_count) }}
                </small>
            </div>
        @endforeach
    @else
        <p style="color:var(--text-muted)">Belum ada cerita. Tambahin lewat "Kelola Cerita" ya!</p>
    @endif
</div>

@else
{{-- ==================== DASHBOARD MEMBER ==================== --}}
{{-- tampilan simpel buat member biasa --}}
<div class="member-dashboard">
    <div class="stats-grid">
        {{-- kartu: total cerita yang dibaca --}}
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="stat-value">{{ $totalStoriesRead ?? 0 }}</div>
            <div class="stat-label">Cerita Dibaca</div>
        </div>
        {{-- kartu: total cerita favorit --}}
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-bookmark"></i></div>
            <div class="stat-value">{{ isset($favorites) ? $favorites->count() : 0 }}</div>
            <div class="stat-label">Cerita Favorit</div>
        </div>
    </div>

    {{-- pesan welcome buat member --}}
    <div class="dash-card">
        <h3><i class="fa-solid fa-sparkles"></i> Selamat Datang!</h3>
        <p style="color:var(--text-muted)">
            Kamu masuk sebagai Member. Jelajahi cerita-cerita seru di 
            <a href="{{ route('home') }}" style="color:var(--primary-color);font-weight:600">Beranda</a>!
        </p>
    </div>
</div>
@endif
@endsection
