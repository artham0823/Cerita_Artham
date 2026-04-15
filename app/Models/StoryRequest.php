<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryRequest extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];

    // relasi database
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
