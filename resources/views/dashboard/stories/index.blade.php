{{-- 
    =====================================================
    VIEW: dashboard/stories/index.blade.php
    =====================================================
    Halaman kelola cerita — tampilin semua cerita dalam tabel,
    plus fitur LIVE SEARCH yang keren banget!
    
    Pas user ngetik di kolom search, hasilnya langsung
    muncul di tabel tanpa perlu klik Enter.
    JavaScript nge-filter baris tabel secara real-time.
    ===================================================== 
--}}
@extends('layouts.dashboard')
@section('title', 'Kelola Cerita - Ceritaku')

@section('content')
{{-- header + tombol tambah cerita --}}
<div class="dash-header">
    <div>
        <h1>Kelola Cerita</h1>
        <p>Lihat dan cari cerita yang ada.</p>
    </div>
</div>

{{-- KOLOM SEARCH — live search, langsung filter pas ngetik --}}
<div class="dash-card" style="margin-bottom: 1rem;">
    <div style="position: relative;">
        {{-- input search —  user ngetik disini --}}
        <input type="text" id="story-search" placeholder="🔍 Cari cerita... (langsung ketik aja, gak perlu Enter)" 
               style="width:100%;padding:0.8rem 1rem;border:1px solid var(--border-color);border-radius:var(--radius-sm);background:var(--bg-main);color:var(--text-main);font-family:'Outfit',sans-serif;font-size:1rem;outline:none;transition:var(--transition);"
               autocomplete="off">
        {{-- info jumlah hasil search --}}
        <small id="search-info" style="color:var(--text-muted);display:block;margin-top:0.5rem;"></small>
    </div>
</div>

{{-- TABEL CERITA — semua cerita + chapter-nya --}}
<div class="dash-card" style="overflow-x:auto">
    <table class="dash-table" id="stories-table">
        {{-- header tabel --}}
        <thead>
            <tr>
                <th>Cover</th>
                <th>Judul</th>
                <th>Genre</th>
                <th>Bab</th>
                <th>Views</th>
                <th>Likes</th>
                <th>Diubah</th>
            </tr>
        </thead>
        <tbody id="stories-tbody">
            {{-- loop semua cerita --}}
            @forelse($stories as $story)
                {{-- baris cerita utama --}}
                <tr class="story-row" data-title="{{ strtolower($story->title) }}" data-genre="{{ strtolower($story->genre) }}">
                    {{-- cover mini --}}
                    <td data-label="Cover"><img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="" style="width:40px;height:55px;object-fit:cover;border-radius:4px"></td>
                    {{-- judul + badge featured --}}
                    <td data-label="Judul">
                        <div style="display: flex; align-items: center; flex-wrap: wrap;">
                            <strong>{{ $story->title }}</strong>
                            @if($story->is_featured) <span class="badge" style="font-size:0.65rem;padding:0.15rem 0.5rem;margin-left:0.3rem">Featured</span> @endif
                        </div>
                    </td>
                    {{-- genre cerita --}}
                    <td data-label="Genre">{{ $story->genre }}</td>
                    {{-- jumlah bab --}}
                    <td data-label="Bab">{{ $story->chapters_count }}</td>
                    {{-- jumlah views --}}
                    <td data-label="Views">{{ number_format($story->views_count) }}</td>
                    {{-- jumlah likes --}}
                    <td data-label="Likes">{{ $story->likes_count }}</td>
                    {{-- tanggal terakhir diubah --}}
                    <td data-label="Diubah"><small>{{ $story->updated_at ? $story->updated_at->format('d M Y') : '-' }}</small></td>
                </tr>
                {{-- baris chapter dari cerita ini --}}
                @foreach($story->chapters as $chapter)
                    <tr class="chapter-row" style="background:var(--bg-accent)" data-parent="{{ strtolower($story->title) }}">
                        <td data-label=""></td>
                        <td data-label="Bab" style="padding-left:2rem"><small style="color:var(--text-muted)">↳</small> {{ $chapter->title }}</td>
                        <td data-label="Info" colspan="4"><small style="color:var(--text-muted)">Bab {{ $chapter->chapter_number }}</small></td>
                        <td data-label="Diubah"><small>{{ $chapter->updated_at ? $chapter->updated_at->format('d M Y') : '-' }}</small></td>
                    </tr>
                @endforeach
            @empty
                {{-- kalo belum ada cerita sama sekali --}}
                <tr id="empty-row"><td colspan="7" class="empty-state" style="display: block; text-align: center;">Belum ada cerita. Jalankan seeder dulu ya!</td></tr>
            @endforelse
        </tbody>
    </table>
    {{-- pesan kalo search gak nemu apa-apa --}}
    <div id="no-results" style="display:none;text-align:center;padding:2rem;color:var(--text-muted);">
        <i class="fa-solid fa-search" style="font-size:2rem;margin-bottom:0.5rem;opacity:0.5;display:block;"></i>
        <p>Gak ada cerita yang cocok nih...</p>
    </div>
</div>
@endsection

{{-- JAVASCRIPT LIVE SEARCH --}}
@push('scripts')
<script>
/**
 * LIVE SEARCH — fitur pencarian real-time
 * cara kerjanya:
 * 1. user ngetik di input search
 * 2. JavaScript langsung filter baris tabel berdasarkan keyword
 * 3. baris yang gak cocok di-hide, yang cocok tetap keliatan
 * 4. info jumlah hasil ditampilin
 * 
 * Gak perlu request ke server — filtering di client-side!
 * Ini bikin search-nya instan tanpa delay.
 */
document.addEventListener('DOMContentLoaded', function() {
    // ambil elemen-elemen yang dibutuhin
    const searchInput = document.getElementById('story-search');  // input search
    const searchInfo = document.getElementById('search-info');    // info hasil
    const noResults = document.getElementById('no-results');      // pesan kosong
    const storyRows = document.querySelectorAll('.story-row');    // baris cerita
    const chapterRows = document.querySelectorAll('.chapter-row'); // baris chapter

    // event listener — tiap kali user ngetik, langsung filter
    searchInput.addEventListener('input', function() {
        // ambil keyword, lowercase biar case-insensitive
        const keyword = this.value.toLowerCase().trim();
        let visibleCount = 0; // counter buat itung cerita yang keliatan

        // loop semua baris cerita
        storyRows.forEach(row => {
            // ambil data judul + genre dari attribute
            const title = row.getAttribute('data-title') || '';
            const genre = row.getAttribute('data-genre') || '';

            // cek apakah keyword ada di judul ATAU genre
            const match = title.includes(keyword) || genre.includes(keyword);

            // show/hide baris cerita
            row.style.display = match ? '' : 'none';

            // show/hide baris chapter yang terkait
            chapterRows.forEach(ch => {
                if (ch.getAttribute('data-parent') === title) {
                    ch.style.display = match ? '' : 'none';
                }
            });

            // kalo cocok, tambah counter
            if (match) visibleCount++;
        });

        // update info jumlah hasil
        if (keyword.length > 0) {
            searchInfo.textContent = `Ditemukan ${visibleCount} cerita yang cocok`;
            // tampilin pesan "gak ketemu" kalo hasil 0
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        } else {
            // kalo search kosong, hapus info + tampilin semua
            searchInfo.textContent = '';
            noResults.style.display = 'none';
        }
    });
});
</script>
@endpush
