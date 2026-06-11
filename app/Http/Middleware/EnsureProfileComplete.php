<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProfileComplete
{
    /**
     * Route yang dikecualikan dari pengecekan profil.
     */
    private array $except = [
        'profile/complete',
        'logout',
        'admin*',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) return $next($request);

        // Skip untuk route yang dikecualikan
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) return $next($request);
        }

        // Cek apakah profil lengkap (NIM dan HP wajib ada)
        if (empty($user->nim) || empty($user->phone)) {
            return redirect()->route('profile.complete')
                ->with('info', 'Lengkapi profilmu terlebih dahulu sebelum menggunakan aplikasi.');
        }

        return $next($request);
    }
}
