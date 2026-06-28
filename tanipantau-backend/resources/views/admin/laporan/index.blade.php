@extends('layouts.admin')

@section('title', 'Laporan Sistem')
@section('page-title', 'Laporan & Rekapitulasi')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100 rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-white-50">Total Petani</h6>
                <h2 class="mb-0 fw-bold">{{ $stats['total_petani'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100 rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-white-50">Total Lahan</h6>
                <h2 class="mb-0 fw-bold">{{ $stats['total_lahan'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100 rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-white-50">Lahan Aktif</h6>
                <h2 class="mb-0 fw-bold">{{ $stats['lahan_aktif'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white h-100 rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-white-50">Total Kunjungan</h6>
                <h2 class="mb-0 fw-bold">{{ $stats['total_kunjungan'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white p-4 border-bottom" style="border-radius: 16px 16px 0 0;">
        <h5 class="m-0" style="font-weight: 600;">Lahan Perlu Tindakan Segera</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Lahan</th>
                        <th>Petani</th>
                        <th>Kondisi Terakhir</th>
                        <th>Catatan</th>
                        <th>Foto</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lahanPerluTindakan as $kunjungan)
                    <tr>
                        <td class="ps-4">{{ $kunjungan->tanggal_kunjungan->format('d/m/Y H:i') }}</td>
                        <td class="fw-medium">{{ $kunjungan->lahan->nama_lahan }}</td>
                        <td>{{ $kunjungan->lahan->petani->nama }}</td>
                        <td><span class="badge bg-danger">{{ $kunjungan->kondisi_lahan }}</span></td>
                        <td><small>{{ Str::limit($kunjungan->catatan, 50) }}</small></td>
                        <td>
                            @if($kunjungan->foto_url)
                                <a href="{{ $kunjungan->foto_url }}" target="_blank">
                                    <img src="{{ $kunjungan->foto_url }}" alt="Foto" style="max-width: 80px; max-height: 80px; border-radius: 8px; cursor: pointer;">
                                </a>
                            @else
                                <div style="width: 80px; height: 80px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 1px solid #E5E7EB;">
                                    <i class="bi bi-image text-muted" style="font-size: 28px; opacity: 0.4;"></i>
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.lahan.index', ['search' => $kunjungan->lahan->nama_lahan]) }}" class="btn btn-sm btn-outline-primary">
                                Lihat Lahan
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-success">
                            <i class="bi bi-check-circle fs-4 d-block mb-2"></i>
                            Tidak ada lahan yang memerlukan tindakan segera
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
