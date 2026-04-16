<?php

// model untuk ngelola data pengguna (sistem role)

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'avatar',
        'title',
        'bio',
        'is_blocked',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_blocked' => 'boolean',
        ];
    }

    // cek rolenya

    public function isAuthor(): bool
    {
        return $this->role === 'author';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function canManageContent(): bool
    {
        return $this->isAuthor() || $this->isAdmin();
    }

    // gamifikasi & leveling 
    public function canUpdateContent(): bool
    {
        return $this->isAuthor() || $this->isAdmin();
    }

    public function getLevelName(): string
    {
        if ($this->isAuthor() || $this->isAdmin()) {
            return 'Pustakawan Royal';
        }

        $count = $this->readingHistories()->count();
        if ($count >= 50) return 'Perpustakaan Berjalan';
        if ($count >= 20) return 'Kutu Buku';
        return 'Pembaca Baru';
    }

    public function getCommentLimit(): int
    {
        if ($this->isAdmin()) return 10;
        
        $level = $this->getLevelName();
        if ($level === 'Perpustakaan Berjalan') return 7;
        if ($level === 'Kutu Buku') return 5;
        
        return 3;
    }

    // limit komen per hari

    public function canComment(): bool
    {
        if ($this->isAuthor()) return true;

        $todayCount = $this->comments()
            ->whereDate('created_at', today())
            ->count();

        return $todayCount < $this->getCommentLimit();
    }

    public function remainingComments(): int|string
    {
        if ($this->isAuthor()) return '∞';

        $todayCount = $this->comments()
            ->whereDate('created_at', today())
            ->count();

        return max(0, $this->getCommentLimit() - $todayCount);
    }

    // relasi database

    public function stories()
    {
        return $this->hasMany(Story::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function readingHistories()
    {
        return $this->hasMany(ReadingHistory::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function storyRequests()
    {
        return $this->hasMany(StoryRequest::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
