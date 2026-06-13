<?php

namespace App\Http\Middleware;

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
                ? \App\Models\ClaimRequest::where('user_id', $request->user()->id)
                    ->whereIn('status', ['approved', 'rejected'])
                    ->with('item:id,name,type')
                    ->latest('updated_at')
                    ->take(10)
                    ->get()
                    ->map(fn ($c) => [
                        'id'           => $c->id,
                        'status'       => $c->status,
                        'item_name'    => $c->item->name ?? 'Barang',
                        'item_id'      => $c->item_id,
                        'has_handover' => !empty($c->handover_photo),
                        'admin_note'   => $c->admin_note,
                        'updated_at'   => $c->updated_at,
                    ])
                : [],
        ]);
    }
}
