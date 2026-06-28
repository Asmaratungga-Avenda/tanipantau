@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Overview')

@push('styles')
<style>
    /* Custom Dashboard Styles overrides */
    .stat-card {
        padding: 24px;
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid var(--border-light);
        background: var(--white);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 4px;
    }
    .stat-primary::before { background: var(--primary); }
    .stat-info::before { background: #3B82F6; }
    .stat-warning::before { background: #F59E0B; }
    .stat-danger::before { background: #EF4444; }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        z-index: 1;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-primary .stat-icon { background: rgba(0, 111, 90, 0.1); color: var(--primary); }
    .stat-info .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3B82F6; }
    .stat-warning .stat-icon { background: rgba(245, 158, 11, 0.1); color: #F59E0B; }
    .stat-danger .stat-icon { background: rgba(239, 68, 68, 0.1); color: #EF4444; }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
        line-height: 1;
    }

    .stat-footer {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 16px;
        font-size: 12px;
        font-weight: 500;
    }

    .trend-up { color: #10B981; }
    .trend-down { color: #EF4444; }
    .trend-neutral { color: var(--text-muted); }

    /* Chart Card */
    .chart-card {
        padding: 24px;
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border-light);
        box-shadow: var(--shadow-sm);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .chart-body {
        flex: 1;
        position: relative;
        min-height: 250px;
        width: 100%;
    }

    /* Timeline / Activity Feed */
    .activity-feed {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .activity-item {
        position: relative;
        padding-left: 30px;
        padding-bottom: 20px;
    }

    .activity-item:last-child {
        padding-bottom: 0;
    }

    .activity-item::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 4px;
        bottom: -4px;
        width: 2px;
        background: var(--border-light);
    }

    .activity-item:last-child::before {
        display: none;
    }

    .activity-marker {
        position: absolute;
        left: 0;
        top: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--white);
        border: 4px solid var(--primary);
        z-index: 1;
    }
    
    .marker-success { border-color: var(--secondary); }
    .marker-warning { border-color: #F59E0B; }
    .marker-danger { border-color: #EF4444; }

    .activity-content {
        background: #F9FAFB;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid var(--border-light);
    }

    .activity-time {
        font-size: 11px;
        color: var(--text-muted);
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .activity-text {
        font-size: 13px;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.4;
    }

    .activity-text strong {
        font-weight: 600;
        color: var(--primary);
    }

    .badge-custom {
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1 -->
    <div class="col-xl-3 col-md-6 animate-slide-up" style="animation-delay: 0.1s;">
        <div class="stat-card stat-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Petani</div>
                    <div class="stat-value">{{ number_format($stats['total_petani']) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="stat-footer">
                @if($trends['petani'] >= 0)
                    <span class="trend-up"><i class="bi bi-arrow-up-right"></i> +{{ $trends['petani'] }}%</span>
                @else
                    <span class="trend-down"><i class="bi bi-arrow-down-right"></i> {{ $trends['petani'] }}%</span>
                @endif
                <span class="text-muted">Bulan ini</span>
            </div>
        </div>
    </div>

    <!-- Stat 2 -->
    <div class="col-xl-3 col-md-6 animate-slide-up" style="animation-delay: 0.2s;">
        <div class="stat-card stat-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Lahan</div>
                    <div class="stat-value">{{ number_format($stats['total_lahan']) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-map-fill"></i>
                </div>
            </div>
            <div class="stat-footer">
                @if($trends['lahan'] >= 0)
                    <span class="trend-up"><i class="bi bi-arrow-up-right"></i> +{{ $trends['lahan'] }}%</span>
                @else
                    <span class="trend-down"><i class="bi bi-arrow-down-right"></i> {{ $trends['lahan'] }}%</span>
                @endif
                <span class="text-muted">Bulan ini</span>
            </div>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="col-xl-3 col-md-6 animate-slide-up" style="animation-delay: 0.3s;">
        <div class="stat-card stat-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Kunjungan</div>
                    <div class="stat-value">{{ number_format($stats['total_kunjungan']) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-clipboard-data-fill"></i>
                </div>
            </div>
            <div class="stat-footer">
                @if($trends['kunjungan'] >= 0)
                    <span class="trend-up"><i class="bi bi-arrow-up-right"></i> +{{ $trends['kunjungan'] }}%</span>
                @else
                    <span class="trend-down"><i class="bi bi-arrow-down-right"></i> {{ $trends['kunjungan'] }}%</span>
                @endif
                <span class="text-muted">Bulan ini</span>
            </div>
        </div>
    </div>

    <!-- Stat 4 -->
    <div class="col-xl-3 col-md-6 animate-slide-up" style="animation-delay: 0.4s;">
        <div class="stat-card stat-danger">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Perlu Tindakan</div>
                    <div class="stat-value">{{ number_format($stats['perlu_tindakan']) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            </div>
            <div class="stat-footer">
                <span class="{{ $stats['perlu_tindakan'] > 0 ? 'trend-down' : 'trend-neutral' }}">
                    <i class="bi bi-activity"></i> Kunjungan terbaru
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart 1: Kunjungan -->
    <div class="col-xl-8 col-lg-7 animate-slide-up" style="animation-delay: 0.5s;">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title">Statistik Kunjungan (Tahun Ini)</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Tahun {{ date('Y') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item active" href="#">Tahun {{ date('Y') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="kunjunganChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Fase Lahan -->
    <div class="col-xl-4 col-lg-5 animate-slide-up" style="animation-delay: 0.6s;">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title">Distribusi Fase Lahan</h5>
            </div>
            <div class="chart-body d-flex justify-content-center align-items-center">
                <canvas id="faseChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Lahan Terbaru -->
    <div class="col-lg-8 animate-slide-up" style="animation-delay: 0.7s;">
        <div class="card h-100">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
                <h5 class="m-0" style="font-size: 16px; font-weight: 600;">Data Lahan Terbaru</h5>
                <a href="{{ route('admin.lahan.index') }}" class="btn btn-sm btn-light" style="font-size: 13px; font-weight: 500;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Lahan</th>
                                <th>Petani</th>
                                <th>Fase</th>
                                <th>Luas (Ha)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lahanTerbaru as $l)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded p-2 text-primary">
                                                <i class="bi bi-map"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $l->nama_lahan }}</div>
                                                <div class="text-muted" style="font-size: 12px;">{{ $l->komoditas }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $l->petani->nama }}</td>
                                    <td>
                                        @php
                                            $faseKey = strtolower(str_replace(' ', '_', $l->fase_lahan));
                                            $badgeClass = $growthPhases[$faseKey]['class'] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} badge-custom">{{ $l->fase_lahan }}</span>
                                    </td>
                                    <td>{{ number_format($l->luas_lahan, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data lahan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="col-lg-4 animate-slide-up" style="animation-delay: 0.8s;">
        <div class="card h-100">
            <div class="card-header bg-white p-4 border-bottom" style="border-radius: 16px 16px 0 0;">
                <h5 class="m-0" style="font-size: 16px; font-weight: 600;">Kunjungan Terbaru</h5>
            </div>
            <div class="card-body p-4">
                <ul class="activity-feed">
                    @forelse($kunjunganTerbaru as $k)
                        <li class="activity-item">
                            @php
                                $markerClass = '';
                                if($k->status_tindak_lanjut == 'Aman') $markerClass = 'marker-success';
                                elseif($k->status_tindak_lanjut == 'Perlu Pemantauan') $markerClass = 'marker-warning';
                                elseif($k->status_tindak_lanjut == 'Perlu Tindakan') $markerClass = 'marker-danger';
                            @endphp
                            <div class="activity-marker {{ $markerClass }}"></div>
                            <div class="activity-content">
                                <div class="activity-time">
                                    <i class="bi bi-clock"></i> 
                                    {{ \Carbon\Carbon::parse($k->tanggal_kunjungan)->diffForHumans() }}
                                </div>
                                <p class="activity-text">
                                    Kunjungan ke lahan <strong>{{ $k->lahan->nama_lahan }}</strong> oleh <strong>{{ $k->petugas->name ?? 'Petugas' }}</strong>.
                                    Kondisi: <span class="badge {{ $kondisiMap[$k->kondisi_lahan]['class'] ?? 'bg-secondary' }}" style="font-size: 10px;">{{ $k->kondisi_lahan }}</span>
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="text-center py-3 text-muted" style="font-size: 13px;">Belum ada aktivitas kunjungan.</li>
                    @endforelse
                </ul>
                @if(count($kunjunganTerbaru) > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.kunjungan.index') }}" class="btn btn-sm btn-light w-100" style="font-size: 13px; font-weight: 500;">
                            Lihat Semua Kunjungan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colors from Design System
    const primaryColor = '#006F5A';
    const secondaryColor = '#3CBF99';
    const accentColor = '#12D98A';
    
    // 1. Line Chart: Kunjungan Bulanan
    const ctxKunjungan = document.getElementById('kunjunganChart');
    if (ctxKunjungan) {
        const rawData = @json($chartData);
        const labels = Object.keys(rawData);
        const data = Object.values(rawData);

        // Create gradient
        const gradient = ctxKunjungan.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(0, 111, 90, 0.4)');
        gradient.addColorStop(1, 'rgba(0, 111, 90, 0.0)');

        new Chart(ctxKunjungan, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: data,
                    borderColor: primaryColor,
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: primaryColor,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Smooth curves
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#002223',
                        titleFont: { family: 'Poppins', size: 13 },
                        bodyFont: { family: 'Poppins', size: 14, weight: 'bold' },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#6B7280', font: { family: 'Poppins', size: 11 } },
                        grid: { color: '#F3F4F6', drawBorder: false }
                    },
                    x: {
                        ticks: { color: '#6B7280', font: { family: 'Poppins', size: 11 } },
                        grid: { display: false, drawBorder: false }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    }

    // 2. Doughnut Chart: Fase Lahan
    const ctxFase = document.getElementById('faseChart');
    if(ctxFase) {
        const faseData = @json($faseData);
        const faseLabels = Object.keys(faseData);
        const faseValues = Object.values(faseData);
        
        // Colors corresponding to phases
        const bgColors = [
            '#6B7280', // Persiapan (Secondary)
            '#3B82F6', // Penanaman (Info)
            '#006F5A', // Pertumbuhan (Primary/Success)
            '#F59E0B', // Panen (Warning)
            '#9CA3AF'  // Panen Selesai (Muted)
        ];

        new Chart(ctxFase, {
            type: 'doughnut',
            data: {
                labels: faseLabels,
                datasets: [{
                    data: faseValues,
                    backgroundColor: bgColors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Poppins', size: 12 },
                            color: '#4B5563'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#002223',
                        bodyFont: { family: 'Poppins', size: 13 },
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });
    }
});
</script>
@endpush
