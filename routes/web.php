<?php

/**
 * =====================================================
 * ROUTES: Web Routes (web.php)
 * =====================================================
 * Semua rute web aplikasi Ceritaku ada di sini!
 * Dikelompokin biar rapi:
 * 
 * 1. Rute Publik — bisa diakses siapapun tanpa login
 * 2. Rute Auth — login, register, logout
 * 3. Rute Authenticated — butuh login dulu
 * 4. Rute Author/Admin — kelola konten (role tertentu)
 * 5. API — endpoint buat live search (JSON)
 * =====================================================
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NavbarController;

// =============================================
// 1. RUTE PUBLIK (gak perlu login)
// =============================================
// halaman beranda — landing page utama
Route::get('/', [HomeController::class, 'index'])->name('home');
// halaman jelajahi — semua cerita + filter genre
Route::get('/explore', [HomeController::class, 'explore'])->name('explore');
// halaman populer — cerita berdasarkan views terbanyak
Route::get('/popular', [HomeController::class, 'popular'])->name('popular');
// halaman pencarian — cari cerita berdasarkan keyword
Route::get('/search', [HomeController::class, 'search'])->name('search');
// halaman detail cerita — liat info cerita + chapter
Route::get('/story/{id}', function ($id) {
    return "Story Detail $id"; })->name('story.show');
// halaman baca chapter — baca isi chapter
Route::get('/story/{storyId}/chapter/{chapterId}', function ($storyId, $chapterId) {
    return "Chapter $chapterId of Story $storyId"; })->name('chapter.show');

// =============================================
// 2. RUTE AUTENTIKASI (login/register/logout)
// =============================================
// halaman login — tampilin form login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
// proses login — validasi username + password
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
// halaman register — tampilin form daftar
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
// proses register — bikin akun member baru
Route::post('/register', [AuthController::class, 'register'])->name('register.process');
// proses logout — keluar dari akun
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =============================================
// 3. RUTE AUTHENTICATED (butuh login)
// =============================================
Route::middleware('auth')->group(function () {
    // dashboard utama — redirect ke dashboard sesuai role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // profil user — sementara masih placeholder
    Route::get('/profile', function () {
        return "Profile"; })->name('profile.edit');

    // request cerita — sementara masih placeholder
    Route::post('/story-request', function () {
        return "Story Request"; })->name('story-request.store');
});

// =============================================
// 4. RUTE AUTHOR/ADMIN (kelola konten)
// =============================================
Route::middleware(['auth', 'role:author,admin'])->group(function () {
    // halaman kelola cerita — tabel + live search
    Route::get('/dashboard/stories', [DashboardController::class, 'stories'])->name('dashboard.stories.index');
});

// =============================================
// 4b. RUTE AUTHOR ONLY (pengaturan website)
// =============================================
Route::middleware(['auth', 'role:author'])->group(function () {
    // kelola navbar — CRUD menu navigasi
    Route::get('/dashboard/navbar', [NavbarController::class, 'index'])->name('dashboard.navbar.index');
    Route::post('/dashboard/navbar', [NavbarController::class, 'store'])->name('dashboard.navbar.store');
    Route::put('/dashboard/navbar/{id}', [NavbarController::class, 'update'])->name('dashboard.navbar.update');
    Route::delete('/dashboard/navbar/{id}', [NavbarController::class, 'destroy'])->name('dashboard.navbar.destroy');
    Route::patch('/dashboard/navbar/{id}/toggle', [NavbarController::class, 'toggle'])->name('dashboard.navbar.toggle');
    Route::post('/dashboard/navbar/reorder', [NavbarController::class, 'reorder'])->name('dashboard.navbar.reorder');
});

// =============================================
// 5. API ENDPOINT (buat live search)
// =============================================
// endpoint JSON buat live search — dipanggil JavaScript tanpa reload halaman
Route::get('/api/search-stories', [DashboardController::class, 'searchStories'])->name('api.search-stories');

