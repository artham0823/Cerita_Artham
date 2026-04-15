<?php

// model untuk cerita yang di favoritkan oleh user

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'story_id',
    ];

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
