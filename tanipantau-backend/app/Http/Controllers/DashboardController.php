<?php

namespace App\Http\Controllers;

use App\Models\Petani;
use App\Models\Lahan;
use App\Models\Kunjungan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk Dashboard API
 * 
 * Menyediakan ringkasan/statistik untuk dashboard
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan ringkasan/statistik dashboard
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $stats = [
            'total_petani' => Petani::count(),
            'total_lahan' => Lahan::count(),
            'total_kunjungan' => Kunjungan::count(),
            'total_petugas' => User::where('role', 'petugas')->count(),
        ];

        // Jika ada user terautentikasi, tambahkan info user
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $stats['user_info'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ];
            if ($user->role === 'petugas') {
                $stats['kunjungan_saya'] = Kunjungan::where('petugas_id', $user->id)->count();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data berhasil diambil',
            'data' => $stats
        ]);
    }

    /**
     * Menampilkan info user saat ini
     * 
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diambil',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]
        ]);
    }
}
