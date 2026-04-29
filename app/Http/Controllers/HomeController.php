<?php

namespace App\Http\Controllers;

// control ke halaman awal/beranda

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\NavbarItem;

class HomeController extends Controller
{
    public function index()
    {
        // Cerita utama (featured) untuk hero section
        $featuredStory = Story::with(['chapters' => function ($q) {
            $q->orderByDesc('chapter_number')->limit(4);
        }])->featured()->first();

        // Semua cerita untuk rekomendasi (kecuali featured)
        $stories = Story::where('is_featured', false)
            ->withCount('chapters')
            ->orderByDesc('views_count')
            ->get();

        // Genre unik untuk filter
        $genres = $this->getUniqueGenres($stories);

        return view('home', compact('featuredStory', 'stories', 'genres'));
    }

    public function explore(Request $request)
    {
        $query = Story::withCount('chapters');

        // Filter buat genre
        if ($request->genre && $request->genre !== 'Semua') {
            $query->where('genre', 'like', "%{$request->genre}%");
        }

        $stories = $query->orderByDesc('created_at')->get();
        $genres = $this->getUniqueGenres(Story::all());

        return view('explore', compact('stories', 'genres'));
    }

    public function popular()
    {
        $stories = Story::withCount('chapters')
            ->orderByDesc('views_count')
            ->get();

        return view('popular', compact('stories'));
    }

    public function search(Request $request)
    {
        $keyword = $request->q;
        $stories = collect();

        if ($keyword) {
            $stories = Story::search($keyword)
                ->withCount('chapters')
                ->get();
        }

        return view('search', compact('stories', 'keyword'));
    }

    private function getUniqueGenres($stories): array
    {
        $genres = [];
        foreach ($stories as $story) {
            if ($story->genre) {
                foreach (explode(',', $story->genre) as $g) {
                    $genres[] = trim($g);
                }
            }
        }
        $genres = array_unique($genres);
        sort($genres);
        return $genres;
    }
}
