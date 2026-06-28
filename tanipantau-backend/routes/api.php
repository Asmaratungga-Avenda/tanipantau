<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetaniController;
use App\Http\Controllers\LahanController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileApiController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\Petugas\LahanController as PetugasLahanController;
use App\Http\Controllers\Petugas\KunjunganController as PetugasKunjunganController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;

/**
 * ============================================
 * PUBLIC ROUTES (Tanpa Autentikasi)
 * Digunakan oleh halaman publik PHP Native
 * ============================================
 */
Route::post('/login', [AuthController::class, 'login']);

// Public read-only endpoints
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/petani', [PetaniController::class, 'index']);
Route::get('/petani/{petani}', [PetaniController::class, 'show']);
Route::get('/lahan', [LahanController::class, 'index']);
Route::get('/lahan/{lahan}', [LahanController::class, 'show']);
Route::get('/kunjungan', [KunjunganController::class, 'index']);
Route::get('/kunjungan/{kunjungan}', [KunjunganController::class, 'show']);

// Wilayah API (Public — data dari API eksternal, cached 24 jam)
Route::get('/kabupaten', [WilayahController::class, 'kabupatens']);
Route::get('/kecamatan/{kabupatenId}', [WilayahController::class, 'kecamatans']);
Route::get('/desa/{kecamatanId}', [WilayahController::class, 'desas']);

/**
 * ============================================
 * PROTECTED ROUTES (Memerlukan Token Sanctum)
 * Digunakan untuk operasi write (CUD)
 * ============================================
 */
Route::middleware(['auth:sanctum'])->group(function () {
    /**
     * Authentication
     */
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [DashboardController::class, 'me']);

    /**
     * Profile Management
     */
    Route::put('/profile', [ProfileApiController::class, 'updateProfile']);
    Route::put('/profile/password', [ProfileApiController::class, 'updatePassword']);

    /**
     * Petani Write Operations (Admin only)
     */
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/petani', [PetaniController::class, 'store']);
        Route::put('/petani/{petani}', [PetaniController::class, 'update']);
        Route::delete('/petani/{petani}', [PetaniController::class, 'destroy']);
    });

    /**
     * Lahan Write Operations (Admin only)
     */
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/lahan', [LahanController::class, 'store']);
        Route::put('/lahan/{lahan}', [LahanController::class, 'update']);
        Route::delete('/lahan/{lahan}', [LahanController::class, 'destroy']);
    });

    /**
     * Kunjungan Write Operations
     * - POST/PUT: Admin + Petugas
     * - DELETE: Admin only
     */
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::post('/kunjungan', [KunjunganController::class, 'store']);
        Route::put('/kunjungan/{kunjungan}', [KunjunganController::class, 'update']);
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/kunjungan/{kunjungan}', [KunjunganController::class, 'destroy']);
    });

    /**
     * ============================================
     * PETUGAS ROUTES (Hanya untuk Petugas)
     * Lahan Saya & Kunjungan Saya
     * ============================================
     */
    Route::prefix('lahan-saya')->middleware(['role:petugas'])->group(function () {
        Route::get('/', [PetugasLahanController::class, 'index']);
        Route::get('/{lahan}', [PetugasLahanController::class, 'show'])
            ->middleware('check.lahan.ownership');
        Route::post('/{lahan}/kunjungan', [PetugasKunjunganController::class, 'store'])
            ->middleware('check.lahan.ownership');
    });

    /**
     * Kunjungan Saya (Petugas)
     */
    Route::prefix('kunjungan-saya')->middleware(['role:petugas'])->group(function () {
        Route::get('/', [PetugasKunjunganController::class, 'index']);
        Route::get('/{kunjungan}', [PetugasKunjunganController::class, 'show'])
            ->middleware('check.kunjungan.ownership');
        Route::put('/{kunjungan}', [PetugasKunjunganController::class, 'update'])
            ->middleware('check.kunjungan.ownership');
        Route::delete('/{kunjungan}', [PetugasKunjunganController::class, 'destroy'])
            ->middleware('check.kunjungan.ownership');
    });

    /**
     * Dashboard Petugas
     */
    Route::get('/dashboard-petugas', [PetugasDashboardController::class, 'index'])
        ->middleware(['role:petugas']);
});