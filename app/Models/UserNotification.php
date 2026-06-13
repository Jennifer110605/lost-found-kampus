<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id', 'type', 'message', 'item_id', 'item_name',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Helper untuk membuat notifikasi baru.
     */
    public static function notify(int $userId, string $type, string $message, ?int $itemId = null, ?string $itemName = null): void
    {
        static::create([
            'user_id'   => $userId,
            'type'      => $type,
            'message'   => $message,
            'item_id'   => $itemId,
            'item_name' => $itemName,
        ]);
    }
}
