<?php

namespace App\Http\Middleware;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'       => $request->user()->id,
                    'name'     => $request->user()->name,
                    'email'    => $request->user()->email,
                    'is_admin' => (bool) $request->user()->is_admin,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'notifications' => fn () => $request->user()
                ? UserNotification::where('user_id', $request->user()->id)
                    ->latest()
                    ->take(15)
                    ->get()
                    ->map(fn ($n) => [
                        'id'         => $n->id,
                        'type'       => $n->type,
                        'message'    => $n->message,
                        'item_id'    => $n->item_id,
                        'item_name'  => $n->item_name,
                        'read'       => !is_null($n->read_at),
                        'created_at' => $n->created_at,
                    ])
                    ->values()
                    ->toArray()
                : [],
            'unread_count' => fn () => $request->user()
                ? UserNotification::where('user_id', $request->user()->id)->unread()->count()
                : 0,
        ]);
    }
}
