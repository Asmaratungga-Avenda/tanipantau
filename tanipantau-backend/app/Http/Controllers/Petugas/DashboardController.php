<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Resources\KunjunganResource;
use App\Models\Kunjungan;
use App\Models\Lahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            // API response
            $userId = Auth::id();
            $stats = [
                'total_lahan_saya' => Lahan::where('petugas_id', $userId)->count(),
                'total_kunjungan_saya' => Kunjungan::where('petugas_id', $userId)->count(),
                'lahan_aktif' => Lahan::where('petugas_id', $userId)->where('status_aktif', true)->count(),
                'kunjungan_terakhir' => KunjunganResource::collection(
                    Kunjungan::with('lahan')
                        ->where('petugas_id', $userId)
                        ->latest('tanggal_kunjungan')
                        ->take(5)
                        ->get()
                ),
            ];
            return response()->json([
                'success' => true,
                'message' => 'Dashboard petugas berhasil diambil',
                'data' => $stats,
            ]);
        }

        // Web view
        $data = $this->dashboardService->getPetugasDashboardData();
        return view('petugas.dashboard', compact('data'));
    }
}
