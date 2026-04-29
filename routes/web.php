<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [HomeController::class, 'explore'])->name('explore');
Route::get('/popular', [HomeController::class, 'popular'])->name('popular');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/story/{id}', function($id) { return "Story Detail $id"; })->name('story.show');
Route::get('/story/{storyId}/chapter/{chapterId}', function($storyId, $chapterId) { return "Chapter $chapterId of Story $storyId"; })->name('chapter.show');

// route sementara
Route::get('/dashboard', function() { return "Dashboard"; })->name('dashboard');
Route::get('/login', function() { return "Login"; })->name('login');
Route::get('/register', function() { return "Register"; })->name('register');
Route::get('/profile', function() { return "Profile"; })->name('profile.edit');
Route::post('/logout', function() { return "Logout"; })->name('logout');
Route::post('/story-request', function() { return "Story Request"; })->name('story-request.store');
