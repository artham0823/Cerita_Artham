@extends('layouts.app')
@section('title', 'Ceritaku - Platform Cerita Digital')

@section('content')
<div class="no-copy">
    {{-- Hero Section --}}
    @if($featuredStory)
    <section class="hero-section">
        <div class="hero-content">
            <span class="badge">Cerita Utama</span>
            <h1>{{ $featuredStory->title }}</h1>
            <p>{{ $featuredStory->description }}</p>
            <a href="{{ route('story.show', $featuredStory->id) }}" class="read-btn">
                Baca Sekarang <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
        <div class="hero-image-wrapper">
            <img src="{{ asset($featuredStory->cover_image) }}" alt="{{ $featuredStory->title }}">
        </div>
    </section>

    {{-- Chapter Terbaru dari Cerita Utama --}}
    <section class="main-story-chapters container">
        <div class="section-header">
            <h2>Chapter Terbaru</h2>
        </div>
        <div class="chapters-grid">
            @foreach($featuredStory->chapters as $chapter)
                <a href="{{ route('chapter.show', [$featuredStory->id, $chapter->id]) }}" class="chapter-card" style="text-decoration:none;color:inherit;animation:fadeInUp 0.5s ease forwards {{ $loop->index * 0.1 }}s;opacity:0">
                    <h3>{{ $chapter->title }}</h3>
                    <p>{{ $chapter->preview }}</p>
                    <span class="chapter-read-btn">
                        <i class="fa-solid fa-book-open"></i> Baca
                    </span>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Rekomendasi Cerita --}}
    <section class="explore-section container">
        <div class="section-header">
            <h2>Rekomendasi Cerita</h2>
            <a href="{{ route('explore') }}" class="view-all">Lihat Semua</a>
        </div>

        {{-- Genre Filters --}}
        <div class="genre-filters">
            <a href="{{ route('home') }}" class="genre-pill active">Semua</a>
            @foreach($genres as $genre)
                <a href="{{ route('explore', ['genre' => $genre]) }}" class="genre-pill">{{ $genre }}</a>
            @endforeach
        </div>

        {{-- Story Cards --}}
        <div class="profiles-grid">
            @forelse($stories as $story)
                <a href="{{ route('story.show', $story->id) }}" class="story-card" style="animation:fadeInUp 0.5s ease forwards {{ ($loop->index % 6) * 0.1 }}s;opacity:0">
                    <div class="story-cover">
                        <img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="{{ $story->title }}" loading="lazy">
                    </div>
                    <div class="story-info">
                        <div class="story-title" title="{{ $story->title }}">{{ $story->title }}</div>
                        <div class="story-meta">
                            <span><i class="fa-solid fa-layer-group"></i> {{ $story->chapters_count ?? $story->chapters->count() }}</span>
                            <span><i class="fa-solid fa-eye"></i> {{ number_format($story->views_count / 1000, 1) }}k</span>
                            <span><i class="fa-solid fa-heart"></i> {{ $story->likes_count }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state" style="grid-column:1/-1">
                    <i class="fa-solid fa-book-open"></i>
                    <p>Belum ada cerita tersedia.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
