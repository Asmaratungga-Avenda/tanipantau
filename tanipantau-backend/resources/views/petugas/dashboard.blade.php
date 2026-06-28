@extends('layouts.petugas')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
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
    .stat-info::before { background: #3CBF99; }
    .stat-warning::before { background: #F59E0B; }

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
    .stat-info .stat-icon { background: rgba(60, 191, 153, 0.1); color: #3CBF99; }
    .stat-warning .stat-icon { background: rgba(245, 158, 11, 0.1); color: #F59E0B; }

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
</style>
@endpush

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1 -->
    <div class="col-xl-4 col-md-6 animate-slide-up" style="animation-delay: 0.1s;">
        <div class="stat-card stat-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Lahan</div>
                    <div class="stat-value">{{ number_format($data['total_lahan'] ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-map-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2 -->
    <div class="col-xl-4 col-md-6 animate-slide-up" style="animation-delay: 0.2s;">
        <div class="stat-card stat-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Kunjungan</div>
                    <div class="stat-value">{{ number_format($data['total_kunjungan'] ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-clipboard2-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="col-xl-4 col-md-6 animate-slide-up" style="animation-delay: 0.3s;">
        <div class="stat-card stat-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Lahan Aktif</div>
                    <div class="stat-value">{{ number_format($data['lahan_aktif'] ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Lahan Terbaru -->
    <div class="col-12 animate-slide-up" style="animation-delay: 0.4s;">
        <div class="card h-100">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
                <h5 class="m-0" style="font-size: 16px; font-weight: 600;">Lahan Saya Terbaru</h5>
                <a href="{{ route('petugas.lahan.index') }}" class="btn btn-sm btn-light" style="font-size: 13px; font-weight: 500;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama Lahan</th>
                                <th>Petani</th>
                                <th>Komoditas</th>
                                <th>Fase</th>
                                <th>Luas (Ha)</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($data['recent_lahan']) && $data['recent_lahan']->count() > 0)
                                @foreach($data['recent_lahan'] as $l)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-light rounded p-2 text-primary">
                                                    <i class="bi bi-map"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $l->nama_lahan }}</div>
                                                    <div class="text-muted" style="font-size: 12px;">{{ $l->desa ?? '' }}, {{ $l->kecamatan ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $l->petani->nama ?? '-' }}</td>
                                        <td>{{ $l->komoditas }}</td>
                                        <td>
                                            <span class="badge" style="background: #E8F5E9; color: #065F46;">{{ $l->fase_lahan }}</span>
                                        </td>
                                        <td>{{ number_format($l->luas_lahan, 2) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('petugas.lahan.show', $l->id) }}" class="btn btn-sm btn-primary-custom" style="padding: 6px 12px; font-size: 12px;">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data lahan</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
