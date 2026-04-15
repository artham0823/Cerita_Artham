<?php

// model untuk ngatur notifikasi dan limit notifikasi

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'actor_username',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createForUser(User $user, string $type, string $message, ?string $actorUsername = null): void
    {
        //  cuma buat author dan admin
        if (!$user->isAuthor() && !$user->isAdmin()) return;

        self::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
            'actor_username' => $actorUsername,
            'created_at' => now(),
        ]);

        // auto cleanup kalo ngelebihin limit
        $limit = $user->isAuthor() ? 100 : 50;
        $count = self::where('user_id', $user->id)->count();

        if ($count > $limit) {
            $deleteCount = $count - $limit;
            $oldIds = self::where('user_id', $user->id)
                ->orderBy('created_at')
                ->limit($deleteCount)
                ->pluck('id');
            self::whereIn('id', $oldIds)->delete();
        }
    }
}
