<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petani;
use App\Models\Lahan;
use App\Models\Kunjungan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Mapping status & kondisi
        $statusMap = [
            'Aman'               => ['label' => 'Aman',               'class' => 'bg-success'],
            'Perlu Pemantauan'   => ['label' => 'Perlu Pemantauan',   'class' => 'bg-warning'],
            'Perlu Tindakan'     => ['label' => 'Perlu Tindakan',     'class' => 'bg-danger'],
        ];

        $kondisiMap = [
            'Sangat Baik'        => ['label' => 'Sangat Baik',        'class' => 'bg-success'],
            'Baik'               => ['label' => 'Baik',               'class' => 'bg-info'],
            'Sedang'             => ['label' => 'Sedang',             'class' => 'bg-warning'],
            'Kurang Baik'        => ['label' => 'Kurang Baik',        'class' => 'bg-secondary'],
            'Sangat Kurang Baik' => ['label' => 'Sangat Kurang Baik', 'class' => 'bg-danger'],
        ];

        // Mapping fase lahan
        $growthPhases = [
            'persiapan'     => ['label' => 'Persiapan',     'class' => 'bg-secondary'],
            'penanaman'     => ['label' => 'Penanaman',     'class' => 'bg-info'],
            'pertumbuhan'   => ['label' => 'Pertumbuhan',   'class' => 'bg-success'],
            'panen'         => ['label' => 'Panen',         'class' => 'bg-warning'],
            'panen_selesai' => ['label' => 'Panen Selesai', 'class' => 'bg-secondary'],
        ];

        $stats = [
            'total_petani'    => Petani::count(),
            'total_lahan'     => Lahan::count(),
            'total_kunjungan' => Kunjungan::count(),
            'perlu_tindakan'  => Kunjungan::where('status_tindak_lanjut', 'Perlu Tindakan')->count(),
        ];

        $startOfThisMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();

        $petaniThisMonth = Petani::where('created_at', '>=', $startOfThisMonth)->count();
        $petaniLastMonth = Petani::whereBetween('created_at', [$startOfLastMonth, $startOfThisMonth])->count();
        $trendPetani = $petaniLastMonth > 0 ? round((($petaniThisMonth - $petaniLastMonth) / $petaniLastMonth) * 100) : ($petaniThisMonth > 0 ? 100 : 0);

        $lahanThisMonth = Lahan::where('created_at', '>=', $startOfThisMonth)->count();
        $lahanLastMonth = Lahan::whereBetween('created_at', [$startOfLastMonth, $startOfThisMonth])->count();
        $trendLahan = $lahanLastMonth > 0 ? round((($lahanThisMonth - $lahanLastMonth) / $lahanLastMonth) * 100) : ($lahanThisMonth > 0 ? 100 : 0);

        $kunjunganThisMonth = Kunjungan::where('tanggal_kunjungan', '>=', $startOfThisMonth)->count();
        $kunjunganLastMonth = Kunjungan::whereBetween('tanggal_kunjungan', [$startOfLastMonth, $startOfThisMonth])->count();
        $trendKunjungan = $kunjunganLastMonth > 0 ? round((($kunjunganThisMonth - $kunjunganLastMonth) / $kunjunganLastMonth) * 100) : ($kunjunganThisMonth > 0 ? 100 : 0);

        $trends = [
            'petani' => $trendPetani,
            'lahan' => $trendLahan,
            'kunjungan' => $trendKunjungan
        ];

        $kunjunganTerbaru = Kunjungan::with(['lahan', 'petugas'])
            ->latest('tanggal_kunjungan')
            ->take(5)
            ->get();

        $lahanTerbaru = Lahan::with('petani')
            ->latest()
            ->take(5)
            ->get();

        $year = date('Y');
        $kunjungansYear = Kunjungan::whereYear('tanggal_kunjungan', $year)->get(['tanggal_kunjungan']);
        
        $monthlyCounts = [];
        foreach ($kunjungansYear as $k) {
            $monthNum = (int)$k->tanggal_kunjungan->format('n');
            $monthlyCounts[$monthNum] = ($monthlyCounts[$monthNum] ?? 0) + 1;
        }

        $allMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $chartData = [];
        foreach ($allMonths as $i => $m) {
            $chartData[$m] = $monthlyCounts[$i + 1] ?? 0;
        }

        $faseDistribution = Lahan::select('fase_lahan')->get()
            ->groupBy('fase_lahan')
            ->map->count()
            ->toArray();

        $allFases = ['Persiapan', 'Penanaman', 'Pertumbuhan', 'Panen', 'Panen Selesai'];
        $faseData = [];
        foreach ($allFases as $f) {
            $faseData[$f] = $faseDistribution[$f] ?? 0;
        }

        return view('admin.dashboard', compact(
            'user', 'stats', 'trends', 'kunjunganTerbaru', 'lahanTerbaru', 'chartData', 'faseData', 'growthPhases', 'statusMap', 'kondisiMap'
        ));
    }
}
