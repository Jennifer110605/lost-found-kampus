<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;


use App\Http\Controllers\ClaimController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/items', [AdminController::class, 'items'])->name('items');
    Route::delete('/items/{item}', [AdminController::class, 'deleteItem'])->name('items.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::get('/claims', [AdminController::class, 'claims'])->name('claims');
    Route::post('/claims/{claim}/approve', [AdminController::class, 'approveClaim'])->name('claims.approve');
    Route::post('/claims/{claim}/reject', [AdminController::class, 'rejectClaim'])->name('claims.reject');
});

// Profile completion (sebelum middleware EnsureProfileComplete aktif)
Route::middleware('auth')->group(function () {
    Route::get('/profile/complete', [\App\Http\Controllers\ProfileController::class, 'complete'])->name('profile.complete');
    Route::post('/profile/complete', [\App\Http\Controllers\ProfileController::class, 'completeStore'])->name('profile.complete.store');
});

// Redirect root ke /items
Route::get('/', fn() => redirect()->route('items.index'));

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // Login / daftar via Google (Socialite)
    Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

// Profile (auth only)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/items/{item}/claim', [ClaimController::class, 'store'])->name('items.claim.store');
    Route::post('/claims/{claim}/handover', [ClaimController::class, 'uploadHandover'])->name('claims.handover');
    // Notifikasi
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Logout
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Daftar item (publik)
Route::get('/items', [ItemController::class, 'index'])->name('items.index');

// ⚠️ PENTING: /items/create HARUS sebelum /items/{item}
Route::middleware('auth')->group(function () {
    Route::get('/my-items', [ItemController::class, 'myItems'])->name('items.my');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('items.comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Detail item (publik) — SETELAH /items/create agar tidak bentrok
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

// Edit & delete (auth only)
Route::middleware('auth')->group(function () {
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
});