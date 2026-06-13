<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Helper: upload file ke Cloudinary, return URL
if (!function_exists('cloudinaryUpload')) {
    function cloudinaryUpload($file, string $folder = 'lost-found'): string {
        return cloudinary()->upload($file->getRealPath(), ['folder' => $folder])->getSecurePath();
    }
    function cloudinaryDelete(string $url): void {
        if (!str_starts_with($url, 'http')) return;
        preg_match('/\/v\d+\/(.+?)(?:\.[a-z]+)?$/', $url, $m);
        if (!empty($m[1])) cloudinary()->destroy($m[1]);
    }
}
use Inertia\Inertia;

class ItemController extends Controller
{
    /**
     * Halaman utama - menampilkan semua postingan
     */
    public function index(Request $request)
    {
        $query = Item::with('user')->latest();

        // Filter berdasarkan tipe (lost/found)
        if ($request->filled('type') && in_array($request->type, ['lost', 'found'])) {
            $query->ofType($request->type);
        }

        // Filter berdasarkan status
        if ($request->filled('status') && in_array($request->status, ['open', 'resolved'])) {
            $query->where('status', $request->status);
        }

        // Pencarian keyword
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $items = $query->paginate(9)->withQueryString();

        return Inertia::render('Items/Index', [
            'items'   => $items,
            'filters' => $request->only(['search', 'type', 'status']),
            'stats'   => [
                'total' => Item::count(),
                'lost'  => Item::where('type', 'lost')->count(),
                'found' => Item::where('type', 'found')->count(),
            ],
        ]);
    }

    /**
     * Form tambah postingan baru
     */
    public function create()
    {
        return Inertia::render('Items/Create');
    }

    /**
     * Simpan postingan baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'          => 'required|in:lost,found',
            'name'          => 'required|string|max:255',
            'description'   => 'required|string|max:1000',
            'location'      => 'required|string|max:255',
            'date_occurred' => 'required|date|before_or_equal:today',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'type.required'          => 'Tipe barang wajib dipilih.',
            'name.required'          => 'Nama barang wajib diisi.',
            'description.required'   => 'Deskripsi wajib diisi.',
            'location.required'      => 'Lokasi wajib diisi.',
            'date_occurred.required' => 'Tanggal kejadian wajib diisi.',
            'date_occurred.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
            'photo.image'            => 'File harus berupa gambar.',
            'photo.max'              => 'Ukuran foto maksimal 2MB.',
        ]);

        // Upload foto jika ada
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('items', 'public');
        }

        Auth::user()->items()->create([
            'type'          => $validated['type'],
            'name'          => $validated['name'],
            'description'   => $validated['description'],
            'location'      => $validated['location'],
            'date_occurred' => $validated['date_occurred'],
            'photo'         => $photoPath,
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Postingan berhasil dibuat!');
    }

    /**
     * Halaman detail satu postingan
     */
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

    /**
     * Form edit postingan (hanya pemilik)
     */
    public function edit(Item $item)
    {
        $this->authorizeItem($item);

        return Inertia::render('Items/Edit', ['item' => $item]);
    }

    /**
     * Simpan perubahan postingan ke database
     */
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
        ], [
            'type.required'          => 'Tipe barang wajib dipilih.',
            'name.required'          => 'Nama barang wajib diisi.',
            'description.required'   => 'Deskripsi wajib diisi.',
            'location.required'      => 'Lokasi wajib diisi.',
            'date_occurred.required' => 'Tanggal kejadian wajib diisi.',
            'status.required'        => 'Status wajib dipilih.',
            'photo.image'            => 'File harus berupa gambar.',
            'photo.max'              => 'Ukuran foto maksimal 2MB.',
        ]);

        // Upload foto baru jika ada, hapus foto lama
        if ($request->hasFile('photo')) {
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }
            $validated['photo'] = $request->file('photo')->store('items', 'public');
        } else {
            // Hapus foto jika user centang hapus foto
            if ($request->boolean('remove_photo') && $item->photo) {
                Storage::disk('public')->delete($item->photo);
                $validated['photo'] = null;
            }
        }

        $item->update($validated);

        return redirect()->route('items.show', $item)
            ->with('success', 'Postingan berhasil diperbarui!');
    }

    /**
     * Hapus postingan (hanya pemilik)
     */
    public function destroy(Item $item)
    {
        $this->authorizeItem($item);

        // Hapus foto dari storage jika ada
        if ($item->photo) {
            Storage::disk('public')->delete($item->photo);
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Postingan berhasil dihapus.');
    }

    /**
     * Halaman postingan milik user yang sedang login
     */
    public function myItems()
    {
        $items = Auth::user()->items()->latest()->paginate(9);

        return Inertia::render('Items/MyItems', ['items' => $items]);
    }

    /**
     * Cek apakah item milik user yang sedang login
     */
    private function authorizeItem(Item $item): void
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak memiliki akses untuk melakukan aksi ini.');
        }
    }
}
