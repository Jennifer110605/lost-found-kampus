<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markRead(UserNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) abort(403);
        $notification->update(['read_at' => now()]);
        return back();
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca.
     */
    public function markAllRead()
    {
        UserNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return back();
    }

    /**
     * Hapus satu notifikasi.
     */
    public function destroy(UserNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) abort(403);
        $notification->delete();
        return back();
    }
}
