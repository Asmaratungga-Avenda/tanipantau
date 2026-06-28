<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\Lahan;
use App\Models\Petani;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $stats = [
            'total_petani'    => Petani::count(),
            'total_lahan'     => Lahan::count(),
            'total_kunjungan' => Kunjungan::count(),
            'lahan_aktif'     => Lahan::where('status_aktif', true)->count(),
        ];

        // Lahan perlu tindakan
        $lahanPerluTindakan = Kunjungan::with('lahan.petani')
            ->where('status_tindak_lanjut', 'Perlu Tindakan')
            ->latest('tanggal_kunjungan')
            ->take(10)
            ->get();

        return view('admin.laporan.index', compact('stats', 'lahanPerluTindakan'));
    }
}
