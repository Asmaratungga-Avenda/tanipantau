@extends('layouts.petugas')

@section('title', 'Kunjungan Saya')
@section('page-title', 'Kunjungan Saya')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3 animate-slide-up">
        <form method="GET" action="{{ route('petugas.kunjungan.index') }}" class="d-flex gap-2">
            <div class="input-group" style="max-width:400px;">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0 bg-light" name="search" value="{{ request('search') }}" placeholder="Cari catatan atau lahan...">
            </div>
            <button type="submit" class="btn btn-primary-custom">Cari</button>
            @if(request('search'))
                <a href="{{ route('petugas.kunjungan.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
        <a href="{{ route('petugas.kunjungan.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-circle me-2"></i> Tambah Kunjungan
        </a>
    </div>

    <div class="card animate-slide-up" style="animation-delay:0.1s;">
        <div class="card-body p-0">
            @if($kunjungan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Lahan</th>
                                <th>Kondisi</th>
                                <th>Status Tindak Lanjut</th>
                                <th>Catatan</th>
                                <th class="text-center">Foto</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kunjungan as $item)
                            <tr>
                                <td class="ps-4">{{ \Carbon\Carbon::parse($item->tanggal_kunjungan)->format('d M Y') }}</td>
                                <td>{{ $item->lahan->nama_lahan ?? '-' }}</td>
                                <td>
                                    @php 
                                        $kondisiColors = [
                                            'Sangat Baik' => 'success',
                                            'Baik' => 'info',
                                            'Sedang' => 'warning',
                                            'Kurang Baik' => 'secondary',
                                            'Sangat Kurang Baik' => 'danger'
                                        ];
                                        $color = $kondisiColors[$item->kondisi_lahan] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }} text-white">{{ $item->kondisi_lahan }}</span>
                                </td>
                                <td>
                                    @php 
                                        $statusColors = [
                                            'Aman' => 'success',
                                            'Perlu Pemantauan' => 'warning',
                                            'Perlu Tindakan' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$item->status_tindak_lanjut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} text-white">{{ $item->status_tindak_lanjut }}</span>
                                </td>
                                <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $item->catatan ?? '-' }}
                                </td>
                                <td class="text-center">
                                    @if($item->foto_url)
                                        <a href="{{ $item->foto_url }}" target="_blank">
                                            <img src="{{ $item->foto_url }}" alt="Foto" style="max-width:60px; max-height:60px; border-radius:8px; cursor:pointer;" loading="lazy" decoding="async">
                                        </a>
                                    @else
                                        <div style="width:60px; height:60px; border-radius:8px; background:#F3F4F6; display:flex; align-items:center; justify-content:center; margin:0 auto; border:1px solid #E5E7EB;">
                                            <i class="bi bi-image text-muted" style="font-size:24px; opacity:0.4;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('petugas.kunjungan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('petugas.kunjungan.destroy', $item->id) }}" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kunjungan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($kunjungan instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $kunjungan->links('vendor.pagination.custom') }}
                    </div>
                @endif
            @else
                <div class="p-5 text-center">
                    <i class="bi bi-clipboard2-check" style="font-size:48px; color:var(--text-muted); opacity:0.4;"></i>
                    <h6 class="mt-3 mb-1">Belum Ada Kunjungan</h6>
                    <p class="text-muted small mb-3">Silakan tambahkan kunjungan pertama Anda</p>
                    <a href="{{ route('petugas.kunjungan.create') }}" class="btn btn-primary-custom">
                        <i class="bi bi-plus-circle me-2"></i> Tambah Kunjungan
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
