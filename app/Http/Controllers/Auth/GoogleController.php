<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Arahkan user ke halaman consent Google.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google: buat akun baru atau login user yang sudah ada.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Gagal login dengan Google. Coba lagi ya.']);
        }

        // Cari user berdasarkan google_id, atau email yang sudah terdaftar sebelumnya.
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Tautkan google_id kalau user ini dulunya daftar manual pakai email.
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $user->avatar ?: $googleUser->getAvatar(),
                ]);
            }
        } else {
            $user = User::create([
                'name'              => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Pengguna',
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
                // Password acak: akun Google tidak login pakai password,
                // tapi kolomnya tetap diisi supaya aman.
                'password'          => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('items.index'))
            ->with('success', 'Selamat datang, ' . $user->name . '!');
    }
}
