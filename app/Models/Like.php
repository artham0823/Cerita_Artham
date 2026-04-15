<?php

// nyimpan data like pada cerita
// semua user termasuk guest bisa like
// guest di track berdasarkan ip address

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'story_id',
        'user_id',
        'ip_address',
    ];

    public function story()
    {
        return $this->belongsTo(Story::class); // cerita seng dilike
    }

    public function user()
    {
        return $this->belongsTo(User::class);  // user seng like )
    }
}
