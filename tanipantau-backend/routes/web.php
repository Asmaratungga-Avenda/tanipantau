<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PetaniController;
use App\Http\Controllers\Admin\LahanController;
use App\Http\Controllers\Admin\KunjunganController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LaporanController;

// Petugas Controllers (web)
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\LahanController as PetugasLahanController;
use App\Http\Controllers\Petugas\KunjunganController as PetugasKunjunganController;

/**
 * ============================================
 * PUBLIC — Landing redirect
 * ============================================
 */
Route::get('/', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->role === 'admin' ? '/admin/dashboard' : '/petugas/dashboard');
    }
    return redirect('/login');
});

/**
 * Storage fallback — serve file dari storage/app/public tanpa symlink
 * Berguna untuk shared hosting (InfinityFree, cPanel) yang tidak support symlink
 */
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');

/**
 * ============================================
 * GUEST — Login (semua role)
 * ============================================
 */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

/**
 * ============================================
 * AUTHENTICATED — Logout (semua role)
 * ============================================
 */
Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

/**
 * ============================================
 * ADMIN ROUTES (prefix: /admin)
 * ============================================
 */
Route::prefix('admin')->middleware(['auth', 'role:admin,petugas,manajer', 'prevent-back'])->group(function () {

    Route::redirect('/', '/admin/dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Profile (semua role)
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password');

    // Petani (admin & manajer)
    Route::middleware('role:admin,manajer')->group(function () {
        Route::get('/petani', [PetaniController::class, 'index'])->name('admin.petani.index');
    });

    // Petani CRUD (admin)
    Route::middleware('role:admin')->group(function () {
        Route::post('/petani', [PetaniController::class, 'store'])->name('admin.petani.store');
        Route::put('/petani/{petani}', [PetaniController::class, 'update'])->name('admin.petani.update');
        Route::delete('/petani/{petani}', [PetaniController::class, 'destroy'])->name('admin.petani.destroy');
    });

    // Lahan (admin & manajer)
    Route::middleware('role:admin,manajer')->group(function () {
        Route::get('/lahan', [LahanController::class, 'index'])->name('admin.lahan.index');
    });

    // Lahan CRUD (admin)
    Route::middleware('role:admin')->group(function () {
        Route::post('/lahan', [LahanController::class, 'store'])->name('admin.lahan.store');
        Route::put('/lahan/{lahan}', [LahanController::class, 'update'])->name('admin.lahan.update');
        Route::delete('/lahan/{lahan}', [LahanController::class, 'destroy'])->name('admin.lahan.destroy');
    });

    // Kunjungan (all admin,manajer,petugas)
    Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('admin.kunjungan.index');

    // Kunjungan CRUD (hanya petugas)
    Route::middleware('role:petugas')->group(function () {
        Route::post('/kunjungan', [KunjunganController::class, 'store'])->name('admin.kunjungan.store');
        Route::put('/kunjungan/{kunjungan}', [KunjunganController::class, 'update'])->name('admin.kunjungan.update');
        Route::delete('/kunjungan/{kunjungan}', [KunjunganController::class, 'destroy'])->name('admin.kunjungan.destroy');
        Route::put('/kunjungan/{kunjungan}/hapus-foto', [KunjunganController::class, 'hapusFoto'])->name('admin.kunjungan.hapus-foto');
    });

    // Users (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Laporan (admin & manajer)
    Route::middleware('role:admin,manajer')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
    });

});

/**
 * ============================================
 * PETUGAS ROUTES (prefix: /petugas)
 * ============================================
 */
Route::prefix('petugas')->middleware(['auth', 'role:petugas', 'prevent-back'])->group(function () {

    Route::redirect('/', '/petugas/dashboard');
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('petugas.dashboard');

    // Lahan Saya
    Route::get('/lahan', [PetugasLahanController::class, 'index'])->name('petugas.lahan.index');
    Route::get('/lahan/{lahan}', [PetugasLahanController::class, 'show'])->name('petugas.lahan.show');

    // Kunjungan Saya
    Route::get('/kunjungan', [PetugasKunjunganController::class, 'index'])->name('petugas.kunjungan.index');
    Route::get('/kunjungan/create', [PetugasKunjunganController::class, 'create'])->name('petugas.kunjungan.create');
    Route::post('/kunjungan', [PetugasKunjunganController::class, 'store'])->name('petugas.kunjungan.store');
    Route::get('/kunjungan/{kunjungan}/edit', [PetugasKunjunganController::class, 'edit'])->name('petugas.kunjungan.edit');
    Route::put('/kunjungan/{kunjungan}', [PetugasKunjunganController::class, 'update'])->name('petugas.kunjungan.update');
    Route::delete('/kunjungan/{kunjungan}', [PetugasKunjunganController::class, 'destroy'])->name('petugas.kunjungan.destroy');

    // Profile (petugas)
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('petugas.profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('petugas.profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('petugas.profile.password');

});
