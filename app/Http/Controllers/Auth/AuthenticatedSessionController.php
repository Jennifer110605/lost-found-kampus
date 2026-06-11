<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan form login (halaman Vue)
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Proses login
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password yang kamu masukkan salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->flash('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');

        // Redirect penuh (aman untuk request Inertia maupun biasa)
        return $request->header('X-Inertia')
            ? Inertia::location(route('items.index'))
            : redirect()->intended(route('items.index'));
    }

    /**
     * Proses logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('items.index')
            ->with('success', 'Kamu berhasil logout.');
    }
}
