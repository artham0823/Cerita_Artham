<?php

// model untuk ngatur riwayat bacaan user

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'story_id',
        'chapter_id',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
