<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: DashboardController
 * =====================================================
 * Ini controller yang ngurusin halaman dashboard.
 * Dashboard itu halaman "belakang layar" — tempat
 * author/admin ngelola cerita, dan member liat
 * statistik bacaan mereka.
 * 
 * Tampilan dashboard beda per role:
 * - Author: statistik lengkap + grafik + kelola cerita
 * - Admin: statistik tanpa kelola admin
 * - Member: statistik bacaan aja
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\User;
use App\Models\Comment;
use App\Models\Chapter;

class DashboardController extends Controller
{
    /**
     * index — halaman utama dashboard
     * redirect ke dashboard yang sesuai berdasarkan role user
     * author dapet dashboard paling lengkap, member paling simpel
     */
    public function index()
    {
        // ambil data user yang lagi login
        $user = auth()->user();

        // cek role, arahin ke dashboard yang sesuai
        if ($user->isAuthor()) {
            return $this->authorDashboard(); // dashboard paling lengkap
        } elseif ($user->isAdmin()) {
            return $this->adminDashboard();  // dashboard lumayan lengkap
        } else {
            return $this->memberDashboard(); // dashboard simpel
        }
    }

    /**
     * authorDashboard — dashboard buat author (yang paling keren)
     * ada statistik lengkap, grafik chart.js, komentar, notifikasi
     * pokoknya semua data bisa diliat dari sini
     */
    private function authorDashboard()
    {
        $user = auth()->user();

        // --- STATISTIK DASAR ---
        $totalStories = Story::count();                                    // total semua cerita
        $totalChapters = Chapter::count();                                 // total semua chapter
        $totalComments = Comment::count();                                 // total semua komentar
        $totalMembers = User::where('role', 'member')->count();           // total member
        $totalAdmins = User::where('role', 'admin')->count();             // total admin

        // --- DATA GRAFIK: distribusi genre cerita ---
        // grupkan cerita berdasarkan genre, hitung masing-masing
        $genreStats = Story::selectRaw('genre, count(*) as count')
            ->groupBy('genre')
            ->pluck('count', 'genre')
            ->toArray();
        $genreLabels = array_keys($genreStats);    // nama-nama genre
        $genreData = array_values($genreStats);    // jumlah per genre

        // --- DATA GRAFIK: aktivitas baca 7 hari terakhir ---
        // ini bakal kosong kalo belum ada reading history
        $readingLabels = [];
        $readingData = [];

        // --- CERITA TERBARU ---
        $recentStories = Story::orderByDesc('created_at')->limit(5)->get();

        // kirim semua data ke view dashboard
        return view('dashboard.index', compact(
            'user', 'totalStories', 'totalChapters', 'totalComments',
            'totalMembers', 'totalAdmins', 'recentStories',
            'genreLabels', 'genreData', 'readingLabels', 'readingData'
        ));
    }

    /**
     * adminDashboard — dashboard buat admin
     * mirip author tapi tanpa kelola admin dan grafik
     */
    private function adminDashboard()
    {
        $user = auth()->user();

        // statistik dasar — sama kayak author minus total admin
        $totalStories = Story::count();
        $totalChapters = Chapter::count();
        $totalComments = Comment::count();
        $totalMembers = User::where('role', 'member')->count();

        // cerita terbaru
        $recentStories = Story::orderByDesc('created_at')->limit(5)->get();

        return view('dashboard.index', compact(
            'user', 'totalStories', 'totalChapters', 'totalComments',
            'totalMembers', 'recentStories'
        ));
    }

    /**
     * memberDashboard — dashboard buat member biasa
     * cuma ada info tentang bacaan dan favorit mereka
     */
    private function memberDashboard()
    {
        $user = auth()->user();

        // total cerita yang pernah dibaca — hitung dari reading histories
        $totalStoriesRead = $user->readingHistories()->distinct('story_id')->count('story_id');

        // daftar favorit
        $favorites = $user->favorites()->with('story')->orderByDesc('created_at')->get();

        return view('dashboard.index', compact('user', 'totalStoriesRead', 'favorites'));
    }

    /**
     * stories — halaman kelola cerita (author/admin)
     * nampilin semua cerita dalam tabel + fitur live search
     * data cerita diambil lengkap sama chapter-chapternya
     */
    public function stories()
    {
        // ambil semua cerita + hitung chapter + load relasi chapters
        $stories = Story::withCount('chapters')
            ->with('chapters')               // eager load chapters biar gak N+1 query
            ->orderByDesc('updated_at')      // urutkan dari yang terbaru di-update
            ->get();

        return view('dashboard.stories.index', compact('stories'));
    }

    /**
     * searchStories — API endpoint buat live search
     * return JSON biar bisa dipake sama JavaScript fetch()
     * dipanggil tiap kali user ngetik di search bar
     */
    public function searchStories(Request $request)
    {
        // ambil keyword dari query parameter
        $keyword = $request->q;

        // kalo keyword kosong, return array kosong
        if (!$keyword) {
            return response()->json([]);
        }

        // cari cerita yang judulnya cocok — pake scope search dari model
        $stories = Story::search($keyword)
            ->select('id', 'title', 'genre', 'cover_image') // ambil field yang perlu aja
            ->limit(10)                                       // batasi 10 hasil
            ->get();

        // return sebagai JSON
        return response()->json($stories);
    }
}
