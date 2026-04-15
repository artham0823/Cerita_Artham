<?php

// model untuk mengelola reaksi pada chapter

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterReaction extends Model
{
    protected $fillable = [
        'chapter_id',
        'user_id',
        'reaction_type',
    ];

    //relasi database
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
