<?php

namespace App\Http\Controllers;

use App\Models\ClaimRequest;
use App\Models\Item;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users'    => User::count(),
                'items'    => Item::count(),
                'lost'     => Item::where('type', 'lost')->count(),
                'found'    => Item::where('type', 'found')->count(),
                'open'     => Item::where('status', 'open')->count(),
                'resolved' => Item::where('status', 'resolved')->count(),
            ],
            'recentItems' => Item::with('user')->latest()->take(6)->get(),
        ]);
    }

    public function items(Request $request)
    {
        $query = Item::with('user')->latest();
        if ($request->search) $query->where('name', 'like', '%' . $request->search . '%');
        if ($request->type)   $query->where('type', $request->type);
        if ($request->status) $query->where('status', $request->status);

        return Inertia::render('Admin/Items', [
            'items'   => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    public function deleteItem(Item $item)
    {
        if ($item->photo) Storage::delete('public/' . $item->photo);
        $item->delete();
        return back()->with('success', 'Postingan berhasil dihapus.');
    }

    public function users(Request $request)
    {
        $query = User::withCount('items')->latest();
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('nim', 'like', '%' . $request->search . '%');
            });
        }
        return Inertia::render('Admin/Users', [
            'users'   => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Tidak bisa mengubah status admin sendiri.');
        $user->update(['is_admin' => !$user->is_admin]);
        return back()->with('success', $user->is_admin ? 'User dijadikan admin.' : 'Status admin dicabut.');
    }

    public function claims(Request $request)
    {
        $query = ClaimRequest::with(['item', 'user'])->latest();
        if ($request->status) $query->where('status', $request->status);

        return Inertia::render('Admin/Claims', [
            'claims'  => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['status']),
            'counts'  => [
                'pending'  => ClaimRequest::where('status', 'pending')->count(),
                'approved' => ClaimRequest::where('status', 'approved')->count(),
                'rejected' => ClaimRequest::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function approveClaim(Request $request, ClaimRequest $claim)
    {
        $request->validate(['note' => 'nullable|string|max:500']);
        $claim->update(['status' => 'approved', 'admin_note' => $request->note]);
        $claim->item->update(['status' => 'resolved']);

        // Notifikasi ke pelapor klaim
        UserNotification::notify(
            $claim->user_id,
            'claim_approved',
            'Klaim kamu untuk "' . ($claim->item->name ?? 'barang') . '" disetujui admin!',
            $claim->item_id,
            $claim->item->name ?? null
        );

        return back()->with('success', "Klaim #{$claim->id} disetujui. Item ditandai selesai.");
    }

    public function rejectClaim(Request $request, ClaimRequest $claim)
    {
        $request->validate(['note' => 'required|string|max:500']);
        $claim->update(['status' => 'rejected', 'admin_note' => $request->note]);

        UserNotification::notify(
            $claim->user_id,
            'claim_rejected',
            'Klaim kamu untuk "' . ($claim->item->name ?? 'barang') . '" ditolak. Catatan: ' . $request->note,
            $claim->item_id,
            $claim->item->name ?? null
        );

        return back()->with('success', "Klaim #{$claim->id} ditolak.");
    }
}
