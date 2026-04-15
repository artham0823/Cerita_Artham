<?php

namespace App\Models;

// model untuk menglola cerita

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'title',
        'content',
        'chapter_number',
    ];

    //relasi database
    public function story()
    {
        return $this->belongsTo(Story::class);          //cerita induk dari chapter ini
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);          //komentar pada chapter ini
    }

    public function readingHistories()
    {
        return $this->hasMany(ReadingHistory::class);   //riwayat bacaan chapter ini
    }

    //helper methods
    public function getPlainTextAttribute(): string
    {
        return strip_tags($this->content);                    //mengambil teks biasa (tanpa HTML) untuk preview
    }

    public function getPreviewAttribute(): string
    {
        return mb_substr($this->plain_text, 0, 150) . '...';  //mengambil preview singkat (150 karakter)
    }
}
