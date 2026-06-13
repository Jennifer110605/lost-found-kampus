<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ItemController extends Controller
{
    // ── Upload foto ke Cloudinary, return secure URL ─────────────
    private function uploadPhoto($file, string $folder = 'lost-found/items'): string
    {
        return cloudinary()->upload($file->getRealPath(), ['folder' => $folder])->getSecurePath();
    }

    // ── Hapus foto dari Cloudinary berdasarkan URL ────────────────
    private function deletePhoto(?string $url): void
    {
        if (!$url || !str_starts_with($url, 'http')) {
            // Local storage fallback
            if ($url) Storage::disk('public')->delete($url);
            return;
        }
        preg_match('/\/v\d+\/(.+?)(?:\.[a-z]+)?$/', $url, $m);
        if (!empty($m[1])) {
            try { cloudinary()->destroy($m[1]); } catch (\Throwable $e) {}
        }
    }

    public function index(Request $request)
    {
        $query = Item::with('user')->latest();

        if ($request->filled('type') && in_array($request->type, ['lost', 'found'])) {
            $query->ofType($request->type);
        }
        if ($request->filled('status') && in_array($request->status, ['open', 'resolved'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        return Inertia::render('Items/Index', [
            'items'   => $query->paginate(9)->withQueryString(),
            'filters' => $request->only(['search', 'type', 'status']),
            'stats'   => [
                'total' => Item::count(),
                'lost'  => Item::where('type', 'lost')->count(),
                'found' => Item::where('type', 'found')->count(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Items/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'          => 'required|in:lost,found',
            'name'          => 'required|string|max:255',
            'description'   => 'required|string|max:1000',
            'location'      => 'required|string|max:255',
            'date_occurred' => 'required|date|before_or_equal:today',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Upload foto ke Cloudinary
        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $this->uploadPhoto($request->file('photo'));
        }

        Auth::user()->items()->create([
            'type'          => $validated['type'],
            'name'          => $validated['name'],
            'description'   => $validated['description'],
            'location'      => $validated['location'],
            'date_occurred' => $validated['date_occurred'],
            'photo'         => $photoUrl,
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Postingan berhasil dibuat!');
    }

    public function show(Item $item)
    {
        $item->load('user', 'comments.user', 'comments.replies.user');

        $userClaim = auth()->check()
            ? $item->claims()->where('user_id', auth()->id())->latest()->first()
            : null;

        return Inertia::render('Items/Show', [
            'item'      => $item,
            'userClaim' => $userClaim,
        ]);
    }

    public function edit(Item $item)
    {
        $this->authorizeItem($item);
        return Inertia::render('Items/Edit', ['item' => $item]);
    }

    public function update(Request $request, Item $item)
    {
        $this->authorizeItem($item);

        $validated = $request->validate([
            'type'          => 'required|in:lost,found',
            'name'          => 'required|string|max:255',
            'description'   => 'required|string|max:1000',
            'location'      => 'required|string|max:255',
            'date_occurred' => 'required|date|before_or_equal:today',
            'status'        => 'required|in:open,resolved',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Hapus foto lama, upload baru ke Cloudinary
            $this->deletePhoto($item->photo);
            $validated['photo'] = $this->uploadPhoto($request->file('photo'));
        } elseif ($request->boolean('remove_photo') && $item->photo) {
            $this->deletePhoto($item->photo);
            $validated['photo'] = null;
        }

        $item->update($validated);

        return redirect()->route('items.show', $item)
            ->with('success', 'Postingan berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        $this->authorizeItem($item);
        $this->deletePhoto($item->photo);
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Postingan berhasil dihapus.');
    }

    public function myItems()
    {
        $items = Auth::user()->items()->latest()->paginate(9);
        return Inertia::render('Items/MyItems', ['items' => $items]);
    }

    private function authorizeItem(Item $item): void
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak memiliki akses untuk melakukan aksi ini.');
        }
    }
}
