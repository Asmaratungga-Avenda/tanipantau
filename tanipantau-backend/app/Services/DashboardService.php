<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Models\Lahan;
use App\Models\Petani;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getAdminDashboardData(): array
    {
        return [
            'total_petani' => Petani::count(),
            'total_lahan' => Lahan::count(),
            'total_kunjungan' => Kunjungan::count(),
            'total_user' => User::count(),
            'recent_kunjungan' => Kunjungan::with(['lahan', 'petugas'])->latest()->take(5)->get(),
        ];
    }

    public function getPetugasDashboardData(): array
    {
        $userId = Auth::id();
        return [
            'total_lahan' => Lahan::where('petugas_id', $userId)->count(),
            'total_kunjungan' => Kunjungan::where('petugas_id', $userId)->count(),
            'lahan_aktif' => Lahan::where('petugas_id', $userId)->where('status_aktif', true)->count(),
            'recent_lahan' => Lahan::where('petugas_id', $userId)
                ->with(['petani'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}
