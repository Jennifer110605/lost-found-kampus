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
            'notifications' => fn () => $this->getNotifications($request),
        ]);
    }

    private function getNotifications(Request $request): array
    {
        if (!$request->user()) return [];

        $userId = $request->user()->id;
        $notifs = collect();

        // 1. Notifikasi klaim (approved/rejected)
        $claimNotifs = \App\Models\ClaimRequest::where('user_id', $userId)
            ->whereIn('status', ['approved', 'rejected'])
            ->with('item:id,name,type')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn ($c) => [
                'id'           => 'claim_' . $c->id,
                'type'         => 'claim',
                'status'       => $c->status,
                'item_name'    => $c->item->name ?? 'Barang',
                'item_id'      => $c->item_id,
                'has_handover' => !empty($c->handover_photo),
                'admin_note'   => $c->admin_note,
                'message'      => $c->status === 'approved'
                    ? 'Klaim kamu disetujui!'
                    : 'Klaim kamu ditolak.',
                'created_at'   => $c->updated_at,
            ]);

        // 2. Notifikasi komentar di postinganmu
        $commentNotifs = \App\Models\Comment::whereHas('item', fn ($q) => $q->where('user_id', $userId))
            ->where('user_id', '!=', $userId)
            ->whereNull('parent_id')
            ->with(['item:id,name', 'user:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($c) => [
                'id'        => 'comment_' . $c->id,
                'type'      => 'comment',
                'status'    => null,
                'item_name' => $c->item->name ?? 'Barang',
                'item_id'   => $c->item_id,
                'message'   => ($c->user->name ?? 'Seseorang') . ' berkomentar di postinganmu',
                'created_at'=> $c->created_at,
            ]);

        // Gabung + urutkan by terbaru + ambil 10
        return $notifs
            ->concat($claimNotifs)
            ->concat($commentNotifs)
            ->sortByDesc('created_at')
            ->take(10)
            ->values()
            ->toArray();
    }
}
