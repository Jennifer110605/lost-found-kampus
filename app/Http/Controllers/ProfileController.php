<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Profile/Index', [
            'user' => [
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'nim'   => $user->nim,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Halaman wajib isi profil (untuk user Google yang baru login).
     */
    public function complete()
    {
        $user = Auth::user();
        return Inertia::render('Profile/Complete', [
            'user' => [
                'name'  => $user->name,
                'email' => $user->email,
                'nim'   => $user->nim,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function completeStore(Request $request)
    {
        $request->validate([
            'nim'   => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        Auth::user()->update([
            'nim'   => $request->nim,
            'phone' => $request->phone,
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Profil berhasil dilengkapi. Selamat datang!');
    }
}