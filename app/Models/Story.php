<?php

// model buat ngelola data cerita

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'genre',
        'views_count',
        'likes_count',
        'is_featured',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'likes_count' => 'integer',
        ];
    }

    // scope untuk filter cerita yang ditampilkan di hero section
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // scope untuk mencari cerita berdasarkan kata kunci judul
    public function scopeSearch($query, string $keyword)
    {
        return $query->where('title', 'like', "%{$keyword}%")
                     ->orWhere('description', 'like', "%{$keyword}%")
                     ->orWhere('genre', 'like', "%{$keyword}%");
    }

    // scope untuk mengurutkan cerita berdasarkan popularitas (views terbanyak)
    public function scopePopular($query)
    {
        return $query->orderByDesc('views_count');
    }

    // relasi database
    //
    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('chapter_number');
    }
 
    // pembuat cerita/author
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // daftar fav
    public function favorites()
    {
        return $this->hashMany(Favorite::class);
    }

    // daftar like
    public function likes()
    {
        return $this->hashMany(Like::class);
    }

    // riwayat bacaan cerita ini
    public function readingHistories()
    {
        return $this->hashMany(ReadingHistory::class);
    }
}
