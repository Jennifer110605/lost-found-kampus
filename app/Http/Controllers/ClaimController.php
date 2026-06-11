<?php

namespace App\Http\Controllers;

use App\Models\ClaimRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    /**
     * Ajukan klaim kepemilikan / penemuan barang.
     */
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'description' => 'required|string|min:20|max:1000',
            'proof_photo' => 'nullable|image|max:2048',
        ], [
            'description.min' => 'Deskripsi bukti minimal 20 karakter.',
        ]);

        // Cek apakah user sudah punya klaim aktif untuk item ini
        $existing = ClaimRequest::where('item_id', $item->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Kamu sudah memiliki klaim aktif untuk barang ini.');
        }

        $proofPath = null;
        if ($request->hasFile('proof_photo')) {
            $proofPath = $request->file('proof_photo')->store('claims/proof', 'public');
        }

        ClaimRequest::create([
            'item_id'     => $item->id,
            'user_id'     => auth()->id(),
            'description' => $request->description,
            'proof_photo' => $proofPath,
        ]);

        return back()->with('success', 'Klaim berhasil diajukan. Tunggu verifikasi dari admin.');
    }

    /**
     * Upload foto dokumentasi serah terima setelah klaim disetujui.
     * Server menambahkan watermark timestamp otomatis untuk mencegah foto palsu.
     */
    public function uploadHandover(Request $request, ClaimRequest $claim)
    {
        $this->authorize('update', $claim);

        $request->validate([
            'handover_photo' => 'required|image|max:5120',
        ]);

        if ($claim->status !== 'approved') {
            return back()->with('error', 'Klaim belum disetujui admin.');
        }

        $file     = $request->file('handover_photo');
        $filename = 'handover_' . $claim->id . '_' . time() . '.jpg';
        $savePath = storage_path('app/public/claims/handover/' . $filename);

        // Buat direktori jika belum ada
        if (!file_exists(dirname($savePath))) {
            mkdir(dirname($savePath), 0755, true);
        }

        // Tambahkan watermark timestamp (via PHP GD)
        $watermarked = $this->addWatermark($file->getRealPath(), $savePath, $claim);

        if (!$watermarked) {
            // Fallback: simpan tanpa watermark jika GD tidak tersedia
            $file->storeAs('claims/handover', $filename, 'public');
        }

        $claim->update([
            'handover_photo' => 'claims/handover/' . $filename,
            'handover_at'    => now(),
        ]);

        return back()->with('success', 'Foto serah terima berhasil diupload. Proses selesai!');
    }

    /**
     * Tambahkan watermark timestamp ke foto (anti-foto palsu).
     * Watermark berisi: tanggal-waktu server + ID klaim + nama barang.
     */
    private function addWatermark(string $sourcePath, string $destPath, ClaimRequest $claim): bool
    {
        if (!extension_loaded('gd')) return false;

        $mime = mime_content_type($sourcePath);
        $img  = match (true) {
            str_contains($mime, 'jpeg') => imagecreatefromjpeg($sourcePath),
            str_contains($mime, 'png')  => imagecreatefrompng($sourcePath),
            str_contains($mime, 'webp') => imagecreatefromwebp($sourcePath),
            default => false,
        };

        if (!$img) return false;

        $w = imagesx($img);
        $h = imagesy($img);

        // Teks watermark
        $timestamp = now()->format('d/m/Y H:i:s');
        $itemName  = $claim->item->name ?? 'Barang';
        $lines     = [
            "SERAH TERIMA BARANG",
            "ID Klaim : #{$claim->id}",
            "Barang   : {$itemName}",
            "Waktu    : {$timestamp}",
            "Lost&Found FATEK UNSRAT",
        ];

        // Background strip hitam semi-transparan di bawah
        $boxH    = count($lines) * 20 + 20;
        $overlay = imagecreatetruecolor($w, $boxH);
        imagefill($overlay, 0, 0, imagecolorallocate($overlay, 0, 0, 0));
        imagecopymerge($img, $overlay, 0, $h - $boxH, 0, 0, $w, $boxH, 70);
        imagedestroy($overlay);

        // Tulis teks
        $white = imagecolorallocate($img, 255, 255, 255);
        $yellow = imagecolorallocate($img, 255, 220, 50);
        foreach ($lines as $i => $line) {
            $color = $i === 0 ? $yellow : $white;
            imagestring($img, 4, 12, $h - $boxH + 10 + ($i * 20), $line, $color);
        }

        imagejpeg($img, $destPath, 90);
        imagedestroy($img);
        return true;
    }
}
