<?php

namespace App\Http\Controllers;

use App\Models\ClaimRequest;
use App\Models\Item;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'description' => 'required|string|min:20|max:1000',
            'proof_photo' => 'nullable|image|max:2048',
        ], [
            'description.min' => 'Deskripsi bukti minimal 20 karakter.',
        ]);

        $existing = ClaimRequest::where('item_id', $item->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Kamu sudah memiliki klaim aktif untuk barang ini.');
        }

        $proofUrl = null;
        if ($request->hasFile('proof_photo')) {
            try {
                $proofUrl = cloudinary()->upload(
                    $request->file('proof_photo')->getRealPath(),
                    ['folder' => 'lost-found/proofs']
                )->getSecurePath();
            } catch (\Throwable $e) {
                // Simpan path lokal sebagai fallback
                $proofUrl = $request->file('proof_photo')->store('proofs', 'public');
            }
        }

        ClaimRequest::create([
            'item_id'     => $item->id,
            'user_id'     => auth()->id(),
            'description' => $request->description,
            'proof_photo' => $proofUrl,
        ]);

        // Notifikasi ke pemilik item
        if ($item->user_id !== auth()->id()) {
            UserNotification::notify(
                $item->user_id,
                'claim_submitted',
                auth()->user()->name . ' mengajukan klaim pada barangmu',
                $item->id,
                $item->name
            );
        }

        return back()->with('success', 'Klaim berhasil diajukan. Tunggu verifikasi dari admin.');
    }

    public function uploadHandover(Request $request, ClaimRequest $claim)
    {
        // Manual auth check (tanpa Policy supaya ga 500)
        if ($claim->user_id !== auth()->id()) {
            abort(403, 'Kamu tidak berhak mengupload foto untuk klaim ini.');
        }

        $request->validate([
            'handover_photo' => 'required|image|max:10240',
        ]);

        if ($claim->status !== 'approved') {
            return back()->with('error', 'Klaim belum disetujui admin.');
        }

        $file     = $request->file('handover_photo');
        $photoUrl = null;

        // Coba tambahkan watermark lalu upload ke Cloudinary
        $tempPath = tempnam(sys_get_temp_dir(), 'handover_') . '.jpg';
        $watermarked = $this->addWatermark($file->getRealPath(), $tempPath, $claim);
        $uploadFrom  = ($watermarked && file_exists($tempPath)) ? $tempPath : $file->getRealPath();

        try {
            $photoUrl = cloudinary()->upload($uploadFrom, ['folder' => 'lost-found/handover'])->getSecurePath();
        } catch (\Throwable $e) {
            // Fallback ke local storage
            $photoUrl = $file->store('handover', 'public');
        }

        // Bersihkan temp file
        if (file_exists($tempPath)) unlink($tempPath);

        $claim->update([
            'handover_photo' => $photoUrl,
            'handover_at'    => now(),
        ]);

        return back()->with('success', 'Foto serah terima berhasil diupload. Proses selesai!');
    }

    private function addWatermark(string $sourcePath, string $destPath, ClaimRequest $claim): bool
    {
        if (!extension_loaded('gd')) return false;

        try {
            $mime = mime_content_type($sourcePath);
            $img  = match (true) {
                str_contains($mime, 'jpeg') => imagecreatefromjpeg($sourcePath),
                str_contains($mime, 'png')  => imagecreatefrompng($sourcePath),
                str_contains($mime, 'webp') => imagecreatefromwebp($sourcePath),
                default => null,
            };

            if (!$img) return false;

            $w = imagesx($img);
            $h = imagesy($img);

            $timestamp = now()->format('d/m/Y H:i:s');
            $itemName  = $claim->item->name ?? 'Barang';
            $lines = [
                "SERAH TERIMA BARANG",
                "ID Klaim : #{$claim->id}",
                "Barang   : {$itemName}",
                "Waktu    : {$timestamp}",
                "Lost&Found FATEK UNSRAT",
            ];

            $boxH    = count($lines) * 20 + 20;
            $overlay = imagecreatetruecolor($w, $boxH);
            imagefill($overlay, 0, 0, imagecolorallocate($overlay, 0, 0, 0));
            imagecopymerge($img, $overlay, 0, $h - $boxH, 0, 0, $w, $boxH, 70);
            imagedestroy($overlay);

            $white  = imagecolorallocate($img, 255, 255, 255);
            $yellow = imagecolorallocate($img, 255, 220, 50);
            foreach ($lines as $i => $line) {
                imagestring($img, 4, 12, $h - $boxH + 10 + ($i * 20), $line, $i === 0 ? $yellow : $white);
            }

            imagejpeg($img, $destPath, 90);
            imagedestroy($img);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
